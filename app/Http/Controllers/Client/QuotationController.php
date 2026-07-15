<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\PaymentSubmittedMail;
use App\Mail\QuotationStatusMail;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
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

        $quotation->load(['items', 'currency', 'tax', 'client', 'user.company', 'payments', 'statusLogs' => function ($q) {
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
            } catch (\Exception $e) {}
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
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string|max:1000',
            'proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment-proofs', 'public');
        }

        $payment = Payment::create([
            'quotation_id'  => $quotation->id,
            'client_user_id' => $request->user('client')->id,
            'amount'        => $validated['amount'],
            'proof'         => $proofPath,
            'notes'         => $validated['notes'],
            'status'        => 'pending',
        ]);

        try {
            Mail::to($quotation->user->email)->send(
                new PaymentSubmittedMail($quotation, $payment, $request->user('client'))
            );
        } catch (\Exception $e) {}

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_submitted',
            'message' => "{$request->user('client')->name} submitted a payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " for {$quotation->quote_number}.",
            'url' => "/quotations/{$quotation->id}",
        ]);

        return back()->with('success', 'Payment submitted successfully. Awaiting company verification.');
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
        } catch (\Exception $e) {}

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

        return back()->with('success', $message);
    }
}
