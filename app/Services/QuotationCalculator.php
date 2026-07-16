<?php

namespace App\Services;

class QuotationCalculator
{
    public static function calculate(array $items, float $taxPercentage, float $discountAmount): array
    {
        $grossTotal = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $grossTotal += $subtotal;
            $itemsData[] = [
                'item_title'       => $item['item_title'],
                'item_description' => $item['item_description'] ?? null,
                'quantity'         => $item['quantity'],
                'unit_price'       => $item['unit_price'],
                'subtotal'         => $subtotal,
                'start_date'       => $item['start_date'] ?? null,
                'end_date'         => $item['end_date'] ?? null,
                'sort_order'       => $item['sort_order'] ?? 0,
            ];
        }

        $taxImpact  = $grossTotal * ($taxPercentage / 100);
        $grandTotal = ($grossTotal + $taxImpact) - $discountAmount;

        return [
            'gross_total' => $grossTotal,
            'tax_impact'  => $taxImpact,
            'grand_total' => max(0, $grandTotal),
            'items_data'  => $itemsData,
        ];
    }
}
