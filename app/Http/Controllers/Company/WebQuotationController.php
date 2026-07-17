<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\ClientWelcomeMail;
use App\Mail\PaymentReminderMail;
use App\Mail\PaymentReviewedMail;
use App\Mail\QuotationStatusMail;
use App\Mail\SendQuotationMail;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
use App\Models\QuotationNote;
use App\Models\QuotationRevision;
use App\Models\QuotationStatusLog;
use App\Models\Tax;
use App\Services\QuotationCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WebQuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::where('user_id', $request->user()->id)
            ->with(['client', 'currency']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $quotations = $query->latest()->paginate(15)->withQueryString();

        return view('company.quotations.index', compact('quotations'));
    }

    public function create(Request $request)
    {
        $clients       = Client::where('user_id', $request->user()->id)->get();
        $currencies    = Currency::active()->get();
        $taxes         = Tax::active()->get();
        $items         = Item::where('user_id', $request->user()->id)->get();
        $defaultTerms  = $request->user()->company?->default_terms;
        $defaultCurrency = Currency::where('is_default', true)->first();
        $accountDetails = $request->user()->company?->account_details;

        return view('company.quotations.create', compact('clients', 'currencies', 'taxes', 'items', 'defaultTerms', 'defaultCurrency', 'accountDetails'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;
        if ($company && !$company->canAddQuotation()) {
            return back()->with('error', 'You have reached your quotation limit. Please upgrade your package.');
        }

        $validated = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'currency_id'       => 'required|exists:currencies,id',
            'tax_id'            => 'nullable|exists:taxes,id',
            'type'              => 'required|in:simple,milestone',
            'issue_date'        => 'required|date',
            'expiry_date'       => 'nullable|date|after_or_equal:issue_date',
            'discount_amount'   => 'required|numeric|min:0',
            'tax_percentage'    => 'required|numeric|min:0|max:100',
            'items'             => 'required|array|min:1',
            'items.*.item_title'       => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.start_date'       => 'nullable|date',
            'items.*.end_date'         => 'nullable|date',
            'terms_conditions'     => 'nullable|string',
            'payment_instructions' => 'nullable|string|max:2000',
            'attachments'          => 'nullable|array|max:5',
            'attachments.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        if ($validated['type'] === 'milestone') {
            $request->validate([
                'items.*.start_date' => 'required|date',
                'items.*.end_date'   => 'required|date|after_or_equal:items.*.start_date',
            ]);
        }

        $client = Client::where('id', $validated['client_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$client) {
            return back()->withErrors(['client_id' => 'Client not found.'])->withInput();
        }

        $quotation = DB::transaction(function () use ($validated, $request) {
            $calc = QuotationCalculator::calculate($validated['items'], $validated['tax_percentage'], $validated['discount_amount']);

            $todayCount = Quotation::whereDate('created_at', now()->toDateString())->lockForUpdate()->count();
            $quoteNumber = 'QT-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'user_id'          => $request->user()->id,
                'client_id'        => $validated['client_id'],
                'currency_id'      => $validated['currency_id'],
                'tax_id'           => $validated['tax_id'] ?? null,
                'quote_number'     => $quoteNumber,
                'type'             => $validated['type'],
                'issue_date'       => $validated['issue_date'],
                'expiry_date'      => $validated['expiry_date'] ?? null,
                'discount_amount'  => $validated['discount_amount'],
                'tax_percentage'   => $validated['tax_percentage'],
                'grand_total'      => $calc['grand_total'],
                'terms_conditions'     => $validated['terms_conditions'] ?? null,
                'payment_instructions' => $validated['payment_instructions'] ?? null,
            ]);

            foreach ($calc['items_data'] as $index => $item) {
                $item['sort_order'] = $index;
                $quotation->items()->create($item);
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('quotation-attachments', 'public');
                    $quotation->attachments()->create([
                        'filename'      => basename($path),
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            return $quotation;
        });

        ActivityLog::log('quotation_created', $quotation, 'Quotation "' . $quotation->quote_number . '" created');

        return redirect('/quotations')->with('success', 'Quotation created successfully!');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        $quotation->load(['client', 'items', 'currency', 'tax', 'notes.user', 'activityLogs.user', 'statusLogs', 'revisions', 'payments.clientUser', 'payments.reviewer', 'attachments']);
        return view('company.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        if (!in_array($quotation->status, ['draft', 'change_requested'])) {
            return back()->with('error', 'Only draft or change-requested quotations can be edited.');
        }

        $clients  = Client::where('user_id', request()->user()->id)->get();
        $currencies = Currency::active()->get();
        $taxes    = Tax::active()->get();
        $items    = Item::where('user_id', request()->user()->id)->get();
        $quotation->load(['items', 'currency', 'tax', 'client', 'attachments']);

        return view('company.quotations.edit', compact('quotation', 'clients', 'currencies', 'taxes', 'items'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        if (!in_array($quotation->status, ['draft', 'change_requested'])) {
            return back()->with('error', 'Only draft or change-requested quotations can be updated.');
        }

        $validated = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'currency_id'       => 'required|exists:currencies,id',
            'tax_id'            => 'nullable|exists:taxes,id',
            'type'              => 'required|in:simple,milestone',
            'issue_date'        => 'required|date',
            'expiry_date'       => 'nullable|date|after_or_equal:issue_date',
            'discount_amount'   => 'required|numeric|min:0',
            'tax_percentage'    => 'required|numeric|min:0|max:100',
            'items'             => 'required|array|min:1',
            'items.*.item_title'       => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.start_date'       => 'nullable|date',
            'items.*.end_date'         => 'nullable|date',
            'terms_conditions'     => 'nullable|string',
            'payment_instructions' => 'nullable|string|max:2000',
            'attachments'          => 'nullable|array|max:5',
            'attachments.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'remove_attachments'   => 'nullable|array',
            'remove_attachments.*' => 'integer|exists:quotation_attachments,id',
        ]);

        if ($validated['type'] === 'milestone') {
            $request->validate([
                'items.*.start_date' => 'required|date',
                'items.*.end_date'   => 'required|date|after_or_equal:items.*.start_date',
            ]);
        }

        DB::transaction(function () use ($validated, $request, $quotation) {
            $calc = QuotationCalculator::calculate($validated['items'], $validated['tax_percentage'], $validated['discount_amount']);

            $wasChangeRequested = $quotation->status === 'change_requested';

            if ($wasChangeRequested) {
                QuotationRevision::create([
                    'quotation_id'    => $quotation->id,
                    'items_data'      => $quotation->items->toArray(),
                    'grand_total'     => $quotation->grand_total,
                    'discount_amount' => $quotation->discount_amount,
                    'tax_percentage'  => $quotation->tax_percentage,
                    'tax_id'          => $quotation->tax_id,
                    'notes'           => 'Auto-archived before amendment',
                    'created_by_type' => get_class($request->user()),
                    'created_by_id'   => $request->user()->id,
                ]);
            }

            $quotation->update([
                'client_id'        => $validated['client_id'],
                'currency_id'      => $validated['currency_id'],
                'tax_id'           => $validated['tax_id'] ?? null,
                'type'             => $validated['type'],
                'issue_date'       => $validated['issue_date'],
                'expiry_date'      => $validated['expiry_date'] ?? null,
                'discount_amount'  => $validated['discount_amount'],
                'tax_percentage'   => $validated['tax_percentage'],
                'grand_total'      => $calc['grand_total'],
                'terms_conditions'     => $validated['terms_conditions'] ?? null,
                'payment_instructions' => $validated['payment_instructions'] ?? null,
                'status'               => $wasChangeRequested ? 'sent' : $quotation->status,
            ]);

            ActivityLog::log('quotation_updated', $quotation, 'Quotation "' . $quotation->quote_number . '" updated');

            $quotation->items()->delete();
            foreach ($calc['items_data'] as $index => $item) {
                $item['sort_order'] = $index;
                $quotation->items()->create($item);
            }

            if (!empty($validated['remove_attachments'])) {
                $quotation->attachments()->whereIn('id', $validated['remove_attachments'])->delete();
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('quotation-attachments', 'public');
                    $quotation->attachments()->create([
                        'filename'      => basename($path),
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            if ($wasChangeRequested) {
                $this->logStatusChange($quotation, 'change_requested', 'sent', $request->user(), 'Quotation amended and re-sent');
                $quotation->update(['viewed_at' => null]);

                $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
                if ($clientEmail) {
                    try {
                        Mail::to($clientEmail)->send(
                            new QuotationStatusMail(
                                $quotation,
                                'change_requested',
                                'sent',
                                'Your requested changes have been applied and the quotation has been re-sent.',
                                $request->user()->name,
                                'client',
                            )
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Email failed (amendment notification): ' . $e->getMessage());
                    }
                }
            }
        });

        return redirect('/quotations/' . $quotation->id)->with('success', 'Quotation updated successfully.');
    }

    public function clone(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $newQuotation = DB::transaction(function () use ($quotation) {
            $todayCount = Quotation::whereDate('created_at', now()->toDateString())->lockForUpdate()->count();
            $quoteNumber = 'QT-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $clone = Quotation::create([
                'user_id'          => $quotation->user_id,
                'client_id'        => $quotation->client_id,
                'currency_id'      => $quotation->currency_id,
                'tax_id'           => $quotation->tax_id,
                'quote_number'     => $quoteNumber,
                'type'             => $quotation->type,
                'issue_date'       => now()->toDateString(),
                'expiry_date'      => null,
                'discount_amount'  => $quotation->discount_amount,
                'tax_percentage'   => $quotation->tax_percentage,
                'grand_total'      => $quotation->grand_total,
                'terms_conditions' => $quotation->terms_conditions,
                'status'           => 'draft',
            ]);

            foreach ($quotation->items()->orderBy('sort_order')->get() as $item) {
                $clone->items()->create($item->only(['item_title', 'item_description', 'quantity', 'unit_price', 'subtotal', 'start_date', 'end_date', 'sort_order']));
            }

            return $clone;
        });

        ActivityLog::log('quotation_cloned', $newQuotation, 'Quotation cloned from "' . $quotation->quote_number . '"');

        return redirect('/quotations/' . $newQuotation->id)
            ->with('success', 'Quotation cloned successfully.');
    }

    public function sendEmail(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        set_time_limit(30);
        ini_set('default_socket_timeout', 10);

        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        $client = $quotation->client;

        $isNewClientUser = false;
        $clientUser = ClientUser::where('email', $client->email)->first();

        try {
            if (!$clientUser) {
                $tempPassword = Str::random(12);
                $clientUser = ClientUser::create([
                    'name'     => $client->name,
                    'email'    => $client->email,
                    'password' => Hash::make($tempPassword),
                    'phone'    => $client->phone,
                    'is_active' => true,
                ]);

                if ($quotation->user->company) {
                    $clientUser->companies()->syncWithoutDetaching([$quotation->user->company->id]);
                }

                Mail::to($client->email)->send(new ClientWelcomeMail($clientUser, $tempPassword, $quotation->quote_number, $quotation->user->company));
                $isNewClientUser = true;
            } else {
                if ($quotation->user->company) {
                    $clientUser->companies()->syncWithoutDetaching([$quotation->user->company->id]);
                }
            }

            if (!$client->client_user_id) {
                $client->update(['client_user_id' => $clientUser->id]);
            }

            Mail::to($client->email)->send(new SendQuotationMail($quotation));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }

        $oldStatus = $quotation->status;
        if ($oldStatus === 'draft') {
            $quotation->update(['status' => 'sent']);
            event(new \App\Events\QuotationStatusChanged($quotation));
        }

        $this->logStatusChange($quotation, $oldStatus, 'sent', request()->user(), $isNewClientUser ? 'Portal account created for client' : null);

        $msg = 'Quotation sent to ' . $client->email;
        if ($isNewClientUser) {
            $msg .= '. A client portal account has been created and login details sent.';
        }

        return back()->with('success', $msg);
    }

    public function amend(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        if ($quotation->status !== 'change_requested') {
            return back()->with('error', 'Only change-requested quotations can be amended.');
        }

        return redirect('/quotations/' . $quotation->id . '/edit');
    }

    public function preview(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        return view('company.quotations.preview', compact('quotation'));
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company', 'attachments']);

        $company = $quotation->user->company;
        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download($quotation->quote_number . '.pdf');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $validated = $request->validate(['status' => 'required|in:sent,accepted,declined']);
        $oldStatus = $quotation->status;
        $quotation->update(['status' => $validated['status']]);

        $this->logStatusChange($quotation, $oldStatus, $validated['status'], $request->user());
        event(new \App\Events\QuotationStatusChanged($quotation));

        $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(
                    new QuotationStatusMail(
                        $quotation,
                        $oldStatus,
                        $validated['status'],
                        null,
                        $request->user()->name,
                        'client',
                    )
                );
            } catch (\Exception $e) {
                \Log::warning('Email failed (status change notification): ' . $e->getMessage());
            }
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'status_changed',
            'message' => "Quotation {$quotation->quote_number} status changed to {$validated['status']}.",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $clientUserId = $quotation->client->client_user_id;
        if ($clientUserId) {
            Notification::create([
                'client_user_id' => $clientUserId,
                'type' => 'status_changed',
                'message' => "Quotation {$quotation->quote_number} status has been changed to " . str_replace('_', ' ', $validated['status']) . ".",
                'url' => "/client/quotations/{$quotation->id}",
            ]);
        }

        return back()->with('success', "Quotation marked as {$validated['status']}.");
    }

    public function updatePayment(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,partial,paid',
            'paid_amount'    => 'nullable|numeric|min:0',
        ]);

        $oldPaymentStatus = $quotation->payment_status ?? 'unpaid';
        $data = ['payment_status' => $validated['payment_status']];
        if ($validated['payment_status'] === 'paid') {
            $data['paid_amount'] = $quotation->grand_total;
            $data['paid_at'] = now();
        } elseif ($validated['payment_status'] === 'partial') {
            $data['paid_amount'] = $validated['paid_amount'] ?? 0;
            $data['paid_at'] = null;
        } else {
            $data['paid_amount'] = null;
            $data['paid_at'] = null;
        }

        $quotation->update($data);

        $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(
                    new QuotationStatusMail(
                        $quotation,
                        $oldPaymentStatus,
                        $validated['payment_status'],
                        "Payment status updated to {$validated['payment_status']}.",
                        $request->user()->name,
                        'client',
                    )
                );
            } catch (\Exception $e) {
                \Log::warning('Email failed (payment status notification): ' . $e->getMessage());
            }
        }

        return back()->with('success', "Payment status updated to {$validated['payment_status']}.");
    }

    public function approvePayment(Request $request, Quotation $quotation, Payment $payment)
    {
        $this->authorizeOwnership($quotation);
        if ($payment->quotation_id !== $quotation->id) abort(404);

        $payment->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $totalPaid = $quotation->payments()->where('status', 'approved')->sum('amount');

        if ($quotation->isMilestone()) {
            $allMilestonesPaid = true;
            foreach ($quotation->items()->get() as $item) {
                $itemPaid = $item->payments()->where('status', 'approved')->sum('amount');
                if ($itemPaid < $item->subtotal) {
                    $allMilestonesPaid = false;
                    break;
                }
            }
            $paymentStatus = $allMilestonesPaid ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
        } else {
            $paymentStatus = $totalPaid >= $quotation->grand_total ? 'paid' : 'partial';
        }

        $quotation->update([
            'payment_status' => $paymentStatus,
            'paid_amount'    => $totalPaid,
            'paid_at'        => $paymentStatus === 'paid' ? now() : null,
        ]);

        $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(
                    new PaymentReviewedMail($quotation, $payment, $request->user()->name)
                );
            } catch (\Exception $e) {
                \Log::warning('Email failed (payment approved notification): ' . $e->getMessage());
            }
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_approved',
            'message' => "Payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " approved for {$quotation->quote_number}.",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $clientUserId = $quotation->client->client_user_id;
        if ($clientUserId) {
            Notification::create([
                'client_user_id' => $clientUserId,
                'type' => 'payment_approved',
                'message' => "Your payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " for {$quotation->quote_number} has been approved.",
                'url' => "/client/quotations/{$quotation->id}",
            ]);
        }

        return back()->with('success', 'Payment approved. Total paid: ' . $quotation->currency_symbol . number_format($totalPaid, 2));
    }

    public function rejectPayment(Request $request, Quotation $quotation, Payment $payment)
    {
        $this->authorizeOwnership($quotation);
        if ($payment->quotation_id !== $quotation->id) abort(404);

        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $payment->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'notes'       => $request->rejection_reason ? ($payment->notes . "\n\nRejection reason: " . $request->rejection_reason) : $payment->notes,
        ]);

        $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(
                    new PaymentReviewedMail($quotation, $payment, $request->user()->name)
                );
            } catch (\Exception $e) {
                \Log::warning('Email failed (payment rejected notification): ' . $e->getMessage());
            }
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_rejected',
            'message' => "Payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " rejected for {$quotation->quote_number}.",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $clientUserId = $quotation->client->client_user_id;
        if ($clientUserId) {
            Notification::create([
                'client_user_id' => $clientUserId,
                'type' => 'payment_rejected',
                'message' => "Your payment of {$quotation->currency_symbol}" . number_format($payment->amount, 2) . " for {$quotation->quote_number} has been rejected.",
                'url' => "/client/quotations/{$quotation->id}",
            ]);
        }

        return back()->with('success', 'Payment rejected.');
    }

    public function bulkApprovePayments(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $request->validate(['payment_ids' => 'required|array|min:1']);

        $payments = Payment::whereIn('id', $request->payment_ids)
            ->where('quotation_id', $quotation->id)
            ->where('status', 'pending')
            ->get();

        if ($payments->isEmpty()) {
            return back()->with('error', 'No valid pending payments selected.');
        }

        foreach ($payments as $payment) {
            $payment->update([
                'status'      => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            try {
                $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
                if ($clientEmail) {
                    Mail::to($clientEmail)->send(
                        new PaymentReviewedMail($quotation, $payment, $request->user()->name)
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Email failed (bulk payment approved): ' . $e->getMessage());
            }
        }

        $totalPaid = $quotation->payments()->where('status', 'approved')->sum('amount');
        $paymentStatus = $totalPaid >= $quotation->grand_total ? 'paid' : 'partial';
        $quotation->update([
            'payment_status' => $paymentStatus,
            'paid_amount'    => $totalPaid,
            'paid_at'        => $paymentStatus === 'paid' ? now() : null,
        ]);

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_approved',
            'message' => $payments->count() . " payments approved for {$quotation->quote_number} (total: {$quotation->currency_symbol}" . number_format($payments->sum('amount'), 2) . ").",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $clientUserId = $quotation->client->client_user_id;
        if ($clientUserId) {
            Notification::create([
                'client_user_id' => $clientUserId,
                'type' => 'payment_approved',
                'message' => $payments->count() . " of your payments for {$quotation->quote_number} have been approved (total: {$quotation->currency_symbol}" . number_format($payments->sum('amount'), 2) . ").",
                'url' => "/client/quotations/{$quotation->id}",
            ]);
        }

        return back()->with('success', $payments->count() . ' payment(s) approved.');
    }

    public function bulkRejectPayments(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $request->validate([
            'payment_ids'       => 'required|array|min:1',
            'rejection_reason'  => 'nullable|string|max:500',
        ]);

        $payments = Payment::whereIn('id', $request->payment_ids)
            ->where('quotation_id', $quotation->id)
            ->where('status', 'pending')
            ->get();

        if ($payments->isEmpty()) {
            return back()->with('error', 'No valid pending payments selected.');
        }

        foreach ($payments as $payment) {
            $notes = $payment->notes;
            if ($request->rejection_reason) {
                $notes = $notes ? $notes . "\n\nRejection reason: " . $request->rejection_reason : "Rejection reason: " . $request->rejection_reason;
            }

            $payment->update([
                'status'      => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'notes'       => $notes,
            ]);

            try {
                $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
                if ($clientEmail) {
                    Mail::to($clientEmail)->send(
                        new PaymentReviewedMail($quotation, $payment, $request->user()->name)
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Email failed (bulk payment rejected): ' . $e->getMessage());
            }
        }

        Notification::create([
            'user_id' => $quotation->user_id,
            'type' => 'payment_rejected',
            'message' => $payments->count() . " payments rejected for {$quotation->quote_number}.",
            'url' => "/quotations/{$quotation->id}",
        ]);

        $clientUserId = $quotation->client->client_user_id;
        if ($clientUserId) {
            Notification::create([
                'client_user_id' => $clientUserId,
                'type' => 'payment_rejected',
                'message' => $payments->count() . " of your payments for {$quotation->quote_number} have been rejected.",
                'url' => "/client/quotations/{$quotation->id}",
            ]);
        }

        return back()->with('success', $payments->count() . ' payment(s) rejected.');
    }

    public function updatePaymentInstructions(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $validated = $request->validate([
            'payment_instructions' => 'nullable|string|max:2000',
        ]);

        $quotation->update(['payment_instructions' => $validated['payment_instructions']]);

        return back()->with('success', 'Payment instructions updated.');
    }

    public function addNote(Request $request, Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        $validated = $request->validate(['note' => 'required|string|max:1000']);

        $quotation->notes()->create([
            'user_id' => $request->user()->id,
            'note'    => $validated['note'],
        ]);

        return back()->with('success', 'Note added.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:quotations,id']);

        $quotationsToDelete = Quotation::whereIn('id', $validated['ids'])
            ->where('user_id', $request->user()->id)
            ->get();

        foreach ($quotationsToDelete as $q) {
            ActivityLog::log('quotation_deleted', $q, 'Quotation "' . $q->quote_number . '" deleted');
        }

        $deleted = $quotationsToDelete->count();
        $quotationsToDelete->delete();

        return redirect('/quotations')->with('success', $deleted . ' quotation(s) deleted.');
    }

    public function exportCsv(Request $request)
    {
        $query = Quotation::where('user_id', $request->user()->id)
            ->with('client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $quotations = $query->latest()->get();

        $filename = 'quotations-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, ['Quote #', 'Client', 'Issue Date', 'Expiry Date', 'Total', 'Status', 'Payment']);

        foreach ($quotations as $q) {
            fputcsv($handle, [
                $q->quote_number,
                $q->client->name,
                $q->issue_date->format('Y-m-d'),
                $q->expiry_date?->format('Y-m-d') ?? '',
                number_format($q->grand_total, 2),
                $q->status,
                $q->payment_status ?? 'unpaid',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function logStatusChange(Quotation $quotation, ?string $from, string $to, $changedBy, ?string $notes = null): void
    {
        QuotationStatusLog::create([
            'quotation_id'    => $quotation->id,
            'from_status'     => $from,
            'to_status'       => $to,
            'changed_by_type' => get_class($changedBy),
            'changed_by_id'   => $changedBy->id,
            'notes'           => $notes,
        ]);
    }

    public function sendReminder(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        if ($quotation->payment_status === 'paid') {
            return back()->with('error', 'This quotation is already fully paid.');
        }

        $clientEmail = $quotation->client->email;
        if (!$clientEmail) {
            return back()->with('error', 'Client has no email address.');
        }

        try {
            Mail::to($clientEmail)->send(new PaymentReminderMail($quotation, request()->user()->name));
        } catch (\Exception $e) {
            \Log::warning('Payment reminder email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send reminder email. Please try again.');
        }

        ActivityLog::log('payment_reminder_sent', $quotation, 'Payment reminder sent for "' . $quotation->quote_number . '"');

        return back()->with('success', 'Payment reminder sent to ' . $clientEmail . '.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorizeOwnership($quotation);

        if ($quotation->payments()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Cannot delete a quotation with pending payments.');
        }

        $quoteNumber = $quotation->quote_number;
        $quotation->delete();
        ActivityLog::log('deleted', null, 'Deleted quotation ' . $quoteNumber);

        return redirect('/quotations')->with('success', 'Quotation ' . $quoteNumber . ' deleted.');
    }

    private function authorizeOwnership(Quotation $quotation): void
    {
        if ($quotation->user_id !== request()->user()->id) {
            abort(403, 'Unauthorized.');
        }
    }
}
