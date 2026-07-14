<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'discount_amount' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_title' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'terms_conditions' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotalSum = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $subtotalSum += $subtotal;

                $itemsData[] = [
                    'item_title' => $item['item_title'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ];
            }

            $taxAmount = $subtotalSum * ($validated['tax_percentage'] / 100);
            $grandTotal = ($subtotalSum + $taxAmount) - $validated['discount_amount'];

            $quoteNumber = 'QT-' . now()->format('Ymd') . '-' . str_pad(Quotation::count() + 1, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'user_id' => auth()->id(),
                'client_id' => $validated['client_id'],
                'quote_number' => $quoteNumber,
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'discount_amount' => $validated['discount_amount'],
                'tax_percentage' => $validated['tax_percentage'],
                'grand_total' => max(0, $grandTotal),
                'terms_conditions' => $validated['terms_conditions'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $quotation->items()->create($item);
            }

            return response()->json([
                'message' => 'Quotation created successfully!',
                'quotation_id' => $quotation->id,
                'quote_number' => $quoteNumber
            ], 210);
        });
    }
}
