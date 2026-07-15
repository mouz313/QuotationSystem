@php $company = $quotation->user->company; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quotation->quote_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8 print:shadow-none print:p-0">
        <div class="flex justify-between items-start border-b-2 border-indigo-600 pb-6 mb-6">
            <div class="flex items-center gap-4">
                @if($company && $company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="Logo" class="w-16 h-16 rounded-lg object-cover">
                @endif
                <div>
                    @if($company)<div class="text-2xl font-bold text-indigo-600">{{ $company->name }}</div>@endif
                    @if($company && $company->email)<div class="text-sm text-gray-500">{{ $company->email }}</div>@endif
                    @if($company && $company->phone)<div class="text-sm text-gray-500">{{ $company->phone }}</div>@endif
                    @if($company && $company->address)<div class="text-sm text-gray-500">{{ $company->address }}</div>@endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-indigo-600 uppercase">Quotation</div>
                <div class="text-lg font-semibold text-gray-700 mt-1">{{ $quotation->quote_number }}</div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">Issue Date</div>
                <div class="font-semibold">{{ $quotation->issue_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">Expiry Date</div>
                <div class="font-semibold">{{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400 uppercase tracking-wide">Status</div>
                <div class="font-semibold capitalize">{{ $quotation->status }}</div>
            </div>
        </div>

        <div class="mb-8">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Bill To</div>
            <div class="font-semibold text-lg">{{ $quotation->client->name }}</div>
            <div class="text-gray-600">{{ $quotation->client->email }}</div>
            @if($quotation->client->phone)<div class="text-gray-600">{{ $quotation->client->phone }}</div>@endif
            @if($quotation->client->address)<div class="text-gray-600">{{ $quotation->client->address }}</div>@endif
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="bg-indigo-600 text-white text-sm uppercase">
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Item</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-right">Qty</th>
                    <th class="p-3 text-right">Unit Price</th>
                    <th class="p-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr class="border-b">
                    <td class="p-3">{{ $index + 1 }}</td>
                    <td class="p-3 font-medium">{{ $item->item_title }}</td>
                    <td class="p-3 text-gray-600">{{ $item->item_description ?? '-' }}</td>
                    <td class="p-3 text-right">{{ $item->quantity }}</td>
                    <td class="p-3 text-right">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="p-3 text-right font-medium">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-72 space-y-2">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal:</span><span class="font-medium">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                @if($quotation->discount_amount > 0)
                <div class="flex justify-between text-sm"><span class="text-gray-500">Discount:</span><span class="text-red-600 font-medium">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
                @endif
                @if($quotation->tax_percentage > 0)
                <div class="flex justify-between text-sm"><span class="text-gray-500">Tax ({{ $quotation->tax_percentage }}%):</span><span class="font-medium">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>
                @endif
                <div class="flex justify-between border-t-2 border-indigo-600 pt-2 text-lg font-bold text-indigo-600"><span>Grand Total:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
            </div>
        </div>

        @if($quotation->terms_conditions)
        <div class="mt-8 pt-6 border-t">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-2">Terms & Conditions</div>
            <p class="text-sm text-gray-600">{{ $quotation->terms_conditions }}</p>
        </div>
        @endif

        <div class="mt-8 text-center text-xs text-gray-400 border-t pt-4">Generated on {{ now()->format('M d, Y \a\t h:i A') }}</div>
    </div>
    <div class="text-center mt-4 print:hidden">
        <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Print</button>
    </div>
</body>
</html>
