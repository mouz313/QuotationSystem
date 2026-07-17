<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\PaymentSubmittedMail;
use App\Mail\QuotationStatusMail;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationStatusLog;
use Barryvdh\DomPDF\Facades\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $clientUser = $request->user('client');
        $companyIds = $clientUser->companies()->pluck('companies.id');
        $companies = $clientUser->companies;

        $query = Quotation::whereHas('user', fn($q) => $q->whereIn('company_id', $companyIds))
            ->where('status', '!=', 'draft')
            ->with(['user.company', 'currency']);

        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $companyId = $request->input('company', '');

        if ($search) {
            $query->where('quote_number', 'like', "%{$search}%");
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($companyId) {
            $query->whereHas('user', fn($q) => $q->where('company_id', $companyId));
        }

        $quotations = $query->latest()->paginate(12)->appends($request->query());

        return view('client.quotations.index', compact('quotations', 'companies', 'search', 'status', 'companyId'));
    }

    public function paymentHistory(Request $request)
    {
        $clientUser = $request->user('client');

        $query = Payment::where('client_user_id', $clientUser->id)
            ->with(['quotation.user.company', 'quotation.currency']);

        $status = $request->input('status', '');
        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(15)->appends($request->query());

        $stats = [
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'pending'  => (clone $query)->where('status', 'pending')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        return view('client.payments.index', compact('payments', 'status', 'stats'));
    }

    public function pdf(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company', 'attachments']);
        $company = $quotation->user->company;

        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download($quotation->quote_number . '.pdf');
    }

    private function userCanAccess(Request $request, Quotation $quotation): bool
    {
        $companyIds = $request->user('client')->companies()->pluck('companies.id');
        return $companyIds->contains($quotation->user->company_id);
    }

    public function show(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        $quotation->load(['items', 'currency', 'tax', 'client', 'user.company', 'payments', 'attachments', 'statusLogs' => function ($q) {
            $q->latest();
        }, 'revisions' => function ($q) {
            $q->latest();
        }]);

        $clientUser = $request->user('client');

        if (!$quotation->viewed_at && $quotation->status === 'sent') {
            $quotation->update(['viewed_at' => now(), 'status' => 'opened']);
            QuotationStatusLog::create([
                'quotation_id'    => $quotation->id,
                'from_status'     => 'sent',
                'to_status'       => 'opened',
                'changed_by_type' => get_class($clientUser),
                'changed_by_id'   => $clientUser->id,
            ]);

            try {
                Mail::to($quotation->user->email)->send(
                    new QuotationStatusMail(
                        $quotation,
                        'sent',
                        'opened',
                        null,
                        $clientUser->name,
                        'company',
                    )
                );
            } catch (\Exception $e) {
                \Log::warning('Email failed (opened status notification): ' . $e->getMessage());
            }
        }

        return view('client.quotations.show', compact('quotation'));
    }

    public function accept(Request $request, Quotation $quotation)
    {
        return $this->changeStatus($request, $quotation, 'accepted');
    }

    public function decline(Request $request, Quotation $quotation)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        return $this->changeStatus($request, $quotation, 'declined', $request->reason);
    }

    public function requestChange(Request $request, Quotation $quotation)
    {
        $request->validate(['notes' => 'required|string|max:1000']);
        return $this->changeStatus($request, $quotation, 'change_requested', $request->notes);
    }

    public function submitPayment(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        if (!in_array($quotation->status, ['sent', 'opened', 'accepted'])) {
            return back()->with('error', 'Payments can only be submitted on active quotations.');
        }

        $validated = $request->validate([
            'quotation_item_id' => 'nullable|exists:quotation_items,id',
            'amount'            => 'required|numeric|min:0.01',
            'notes'             => 'nullable|string|max:1000',
            'proof'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($quotation->isMilestone()) {
            if (empty($validated['quotation_item_id'])) {
                return back()->with('error', 'Please select a milestone to pay for.');
            }
            $item = $quotation->items()->find($validated['quotation_item_id']);
            if (!$item) {
                return back()->with('error', 'Invalid milestone selected.');
            }
            $paidAmount = $item->paid_amount;
            $remaining = max(0, $item->subtotal - $paidAmount);
            if ($validated['amount'] > $remaining) {
                return back()->with('error', "Amount exceeds remaining balance of {$quotation->currency_symbol}" . number_format($remaining, 2) . " for this milestone.");
            }
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment-proofs', 'public');
        }

        $payment = Payment::create([
            'quotation_id'      => $quotation->id,
            'quotation_item_id' => $validated['quotation_item_id'] ?? null,
            'client_user_id'    => $request->user('client')->id,
            'amount'            => $validated['amount'],
            'proof'             => $proofPath,
            'notes'             => $validated['notes'],
            'status'            => 'pending',
        ]);

        try {
            Mail::to($quotation->user->email)->send(
                new PaymentSubmittedMail($quotation, $payment, $request->user('client'))
            );
        } catch (\Exception $e) {
            \Log::warning('Email failed (payment submitted notification): ' . $e->getMessage());
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_submitted',
            'message' => "{$request->user('client')->name} submitted a payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " for {$quotation->quote_number}" . ($payment->quotation_item_id ? " (milestone: {$item->item_title})" : '') . ".",
            'url' => "/quotations/{$quotation->id}",
        ]);

        return back()->with('success', 'Payment submitted successfully. Awaiting company verification.');
    }

    public function paymentReceipt(Request $request, Quotation $quotation)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        $quotation->load(['items', 'currency', 'tax', 'client', 'user.company', 'payments' => function ($q) {
            $q->where('status', 'approved')->with('clientUser', 'quotationItem');
        }]);

        if ($quotation->payments->isEmpty()) {
            return back()->with('error', 'No approved payments to generate a receipt for.');
        }

        $company = $quotation->user->company ?? null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('client.quotations.payment-receipt', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download('Payment-Receipt-' . $quotation->quote_number . '.pdf');
    }

    private function changeStatus(Request $request, Quotation $quotation, string $newStatus, ?string $notes = null)
    {
        if (!$this->userCanAccess($request, $quotation)) {
            abort(403);
        }

        $allowed = [
            'sent'             => ['opened', 'accepted', 'declined', 'change_requested'],
            'opened'           => ['accepted', 'declined', 'change_requested'],
            'change_requested' => ['accepted', 'declined'],
        ];

        $currentStatus = $quotation->status;
        $validNextStatuses = $allowed[$currentStatus] ?? [];

        if (!in_array($newStatus, $validNextStatuses)) {
            return back()->with('error', "Cannot change status from '{$currentStatus}' to '{$newStatus}'.");
        }

        $oldStatus = $quotation->status;
        $quotation->update(['status' => $newStatus]);

        QuotationStatusLog::create([
            'quotation_id'    => $quotation->id,
            'from_status'     => $oldStatus,
            'to_status'       => $newStatus,
            'changed_by_type' => get_class($request->user('client')),
            'changed_by_id'   => $request->user('client')->id,
            'notes'           => $notes,
        ]);

        $clientUser = $request->user('client');
        try {
            Mail::to($quotation->user->email)->send(
                new QuotationStatusMail(
                    $quotation,
                    $oldStatus,
                    $newStatus,
                    $notes,
                    $clientUser->name,
                    'company',
                )
            );
        } catch (\Exception $e) {
            \Log::warning('Email failed (status change notification): ' . $e->getMessage());
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'status_changed',
            'message' => "{$clientUser->name} changed {$quotation->quote_number} to " . str_replace('_', ' ', $newStatus) . ".",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $message = match ($newStatus) {
            'accepted'        => 'Quotation accepted successfully.',
            'declined'        => 'Quotation declined.',
            'change_requested' => 'Change request submitted.',
            default           => "Status changed to {$newStatus}.",
        };

        if ($newStatus === 'accepted' && !$quotation->invoice_number) {
            $quotation->generateInvoiceNumber();
        }

        return back()->with('success', $message);
    }
}
