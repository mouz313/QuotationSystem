<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Quotation;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebQuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::where('user_id', $request->user()->id)
            ->with(['client', 'currency'])
            ->latest()
            ->paginate(15);

        return view('company.quotations.index', compact('quotations'));
    }

    public function create(Request $request)
    {
        $clients   = Client::where('user_id', $request->user()->id)->get();
        $currencies = Currency::active()->get();
        $taxes      = Tax::active()->get();

        return view('company.quotations.create', compact('clients', 'currencies', 'taxes'));
    }

    public function store(Request $request)
    {
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
        $quotation->load(['client', 'items', 'currency', 'tax']);
        return view('company.quotations.show', compact('quotation'));
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate(['status' => 'required|in:sent,accepted,declined']);
        $quotation->update(['status' => $validated['status']]);

        return back()->with('success', "Quotation marked as {$validated['status']}.");
    }
}
