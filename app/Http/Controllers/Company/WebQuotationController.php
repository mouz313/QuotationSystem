<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\SendQuotationMail;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Quotation;
use App\Models\QuotationNote;
use App\Models\Tax;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        return view('company.quotations.create', compact('clients', 'currencies', 'taxes', 'items', 'defaultTerms', 'defaultCurrency'));
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
            'issue_date'        => 'required|date',
            'expiry_date'       => 'nullable|date|after_or_equal:issue_date',
            'discount_amount'   => 'required|numeric|min:0',
            'tax_percentage'    => 'required|numeric|min:0|max:100',
            'items'             => 'required|array|min:1',
            'items.*.item_title'       => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'terms_conditions'  => 'nullable|string',
        ]);

        $client = Client::where('id', $validated['client_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$client) {
            return back()->withErrors(['client_id' => 'Client not found.'])->withInput();
        }

        DB::transaction(function () use ($validated, $request) {
            $grossTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $grossTotal += $subtotal;
                $itemsData[] = [
                    'item_title'       => $item['item_title'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'],
                    'subtotal'         => $subtotal,
                ];
            }

            $taxImpact  = $grossTotal * ($validated['tax_percentage'] / 100);
            $grandTotal = ($grossTotal + $taxImpact) - $validated['discount_amount'];

            $quoteNumber = 'QT-' . now()->format('Ymd') . '-' . str_pad(
                Quotation::whereDate('created_at', now()->toDateString())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $quotation = Quotation::create([
                'user_id'          => $request->user()->id,
                'client_id'        => $validated['client_id'],
                'currency_id'      => $validated['currency_id'],
                'tax_id'           => $validated['tax_id'] ?? null,
                'quote_number'     => $quoteNumber,
                'issue_date'       => $validated['issue_date'],
                'expiry_date'      => $validated['expiry_date'] ?? null,
                'discount_amount'  => $validated['discount_amount'],
                'tax_percentage'   => $validated['tax_percentage'],
                'grand_total'      => max(0, $grandTotal),
                'terms_conditions' => $validated['terms_conditions'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $quotation->items()->create($item);
            }
        });

        return redirect('/quotations')->with('success', 'Quotation created successfully!');
    }

    public function show(Quotation $quotation)
    {
        if ($quotation->user_id !== request()->user()->id) abort(403);
        $quotation->load(['client', 'items', 'currency', 'tax', 'notes.user', 'activityLogs.user']);
        return view('company.quotations.show', compact('quotation'));
    }

    public function clone(Quotation $quotation)
    {
        if ($quotation->user_id !== request()->user()->id) abort(403);

        $newQuotation = DB::transaction(function () use ($quotation) {
            $clone = Quotation::create([
                'user_id'          => $quotation->user_id,
                'client_id'        => $quotation->client_id,
                'currency_id'      => $quotation->currency_id,
                'tax_id'           => $quotation->tax_id,
                'quote_number'     => $quotation->quote_number . '-COPY',
                'issue_date'       => now()->toDateString(),
                'expiry_date'      => null,
                'discount_amount'  => $quotation->discount_amount,
                'tax_percentage'   => $quotation->tax_percentage,
                'grand_total'      => $quotation->grand_total,
                'terms_conditions' => $quotation->terms_conditions,
                'status'           => 'draft',
            ]);

            foreach ($quotation->items as $item) {
                $clone->items()->create($item->only(['item_title', 'item_description', 'quantity', 'unit_price', 'subtotal']));
            }

            return $clone;
        });

        return redirect('/quotations/' . $newQuotation->id)
            ->with('success', 'Quotation cloned successfully.');
    }

    public function sendEmail(Quotation $quotation)
    {
        if ($quotation->user_id !== request()->user()->id) abort(403);

        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        Mail::to($quotation->client->email)->send(new SendQuotationMail($quotation));

        if ($quotation->status === 'draft') {
            $quotation->update(['status' => 'sent']);
            event(new \App\Events\QuotationStatusChanged($quotation));
        }

        return back()->with('success', 'Quotation sent to ' . $quotation->client->email);
    }

    public function preview(Quotation $quotation)
    {
        if ($quotation->user_id !== request()->user()->id) abort(403);
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);
        return view('company.quotations.preview', compact('quotation'));
    }

    public function pdf(Quotation $quotation)
    {
        if ($quotation->user_id !== request()->user()->id) abort(403);
        $quotation->load(['client', 'items', 'currency', 'tax', 'user.company']);

        $company = $quotation->user->company;
        $pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation', 'company'));
        $pdf->setOption('isRemoteEnabled', true);
        return $pdf->download($quotation->quote_number . '.pdf');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate(['status' => 'required|in:sent,accepted,declined']);
        $quotation->update(['status' => $validated['status']]);

        event(new \App\Events\QuotationStatusChanged($quotation));

        return back()->with('success', "Quotation marked as {$validated['status']}.");
    }

    public function updatePayment(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,partial,paid',
            'paid_amount'    => 'nullable|numeric|min:0',
        ]);

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

        return back()->with('success', "Payment status updated to {$validated['payment_status']}.");
    }

    public function addNote(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) abort(403);

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

        $deleted = Quotation::whereIn('id', $validated['ids'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return redirect('/quotations')->with('success', $deleted . ' quotation(s) deleted.');
    }

    public function exportCsv(Request $request)
    {
        $quotations = Quotation::where('user_id', $request->user()->id)
            ->with('client')
            ->latest()
            ->get();

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
}
