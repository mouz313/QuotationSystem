<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quotation;
use App\Services\QuotationCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'currency_id'       => 'required|exists:currencies,id',
            'tax_id'            => 'nullable|exists:taxes,id',
            'type'              => 'nullable|in:simple,milestone',
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
            'terms_conditions'  => 'nullable|string',
        ]);

        if (($validated['type'] ?? 'simple') === 'milestone') {
            $request->validate([
                'items.*.start_date' => 'required|date',
                'items.*.end_date'   => 'required|date|after_or_equal:items.*.start_date',
            ]);
        }

        $client = Client::where('id', $validated['client_id'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Client not found or access denied.',
            ], 403);
        }

        return DB::transaction(function () use ($validated) {
            $calc = QuotationCalculator::calculate($validated['items'], $validated['tax_percentage'], $validated['discount_amount']);

            $todayCount = Quotation::whereDate('created_at', now()->toDateString())->lockForUpdate()->count();
            $quoteNumber = 'QT-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'user_id'           => auth()->id(),
                'client_id'         => $validated['client_id'],
                'currency_id'       => $validated['currency_id'],
                'tax_id'            => $validated['tax_id'] ?? null,
                'quote_number'      => $quoteNumber,
                'type'              => $validated['type'] ?? 'simple',
                'issue_date'        => $validated['issue_date'],
                'expiry_date'       => $validated['expiry_date'] ?? null,
                'discount_amount'   => $validated['discount_amount'],
                'tax_percentage'    => $validated['tax_percentage'],
                'grand_total'       => $calc['grand_total'],
                'terms_conditions'  => $validated['terms_conditions'] ?? null,
            ]);

            foreach ($calc['items_data'] as $index => $item) {
                $item['sort_order'] = $index;
                $quotation->items()->create($item);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Quotation transactional model successfully synchronized.',
                'data'    => [
                    'quotation_id' => $quotation->id,
                    'quote_number' => $quoteNumber,
                    'grand_total'  => number_format($calc['grand_total'], 2, '.', ''),
                ],
            ], 201);
        });
    }
}
