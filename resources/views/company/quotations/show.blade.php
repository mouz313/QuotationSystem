@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $quotation->quote_number }}</h1>
        <p class="text-sm text-gray-500">Issued {{ $quotation->issue_date->format('M d, Y') }}</p>
    </div>
    <div class="flex gap-2">
        @if($quotation->status === 'draft')
            <form method="POST" action="/quotations/{{ $quotation->id }}/status">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="sent">
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Mark as Sent</button>
            </form>
        @endif
        <a href="/quotations" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Back</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-500 mb-1">Client</h3>
            <div class="font-medium">{{ $quotation->client->name }}</div>
            <div class="text-sm text-gray-600">{{ $quotation->client->email }}</div>
            @if($quotation->client->phone)<div class="text-sm text-gray-600">{{ $quotation->client->phone }}</div>@endif
        </div>
        <div class="text-right">
            <div class="mb-2">
                @if($quotation->status === 'draft')<span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Draft</span>
                @elseif($quotation->status === 'sent')<span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700">Sent</span>
                @elseif($quotation->status === 'accepted')<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">Accepted</span>
                @else<span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700">Declined</span>
                @endif
            </div>
            <div class="text-sm text-gray-500">Expiry: {{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</div>
            @if($quotation->currency)
                <div class="text-sm text-gray-500 mt-1">Currency: <span class="font-medium">{{ $quotation->currency->symbol }} {{ $quotation->currency->code }}</span></div>
            @endif
        </div>
    </div>

    <table class="w-full text-sm mb-6">
        <thead><tr class="text-left text-gray-500 border-b">
            <th class="pb-2">Item</th><th class="pb-2">Description</th><th class="pb-2 text-right">Qty</th><th class="pb-2 text-right">Price</th><th class="pb-2 text-right">Subtotal</th>
        </tr></thead>
        <tbody>
        @foreach($quotation->items as $item)
            <tr class="border-b">
                <td class="py-3 font-medium">{{ $item->item_title }}</td>
                <td class="py-3 text-gray-600">{{ $item->item_description ?? '-' }}</td>
                <td class="py-3 text-right">{{ $item->quantity }}</td>
                <td class="py-3 text-right">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                <td class="py-3 text-right font-medium">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-end">
        <div class="w-64 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
            @if($quotation->discount_amount > 0)
                <div class="flex justify-between"><span class="text-gray-500">Discount:</span><span class="text-red-600">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
            @endif
            @if($quotation->tax_percentage > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">Tax @if($quotation->tax)({{ $quotation->tax->name }}) @endif({{ $quotation->tax_percentage }}%):</span>
                    <span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between border-t pt-2 text-lg font-bold"><span>Grand Total:</span><span class="text-indigo-600">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
        </div>
    </div>

    @if($quotation->terms_conditions)
        <div class="mt-6 pt-4 border-t">
            <h4 class="text-sm font-semibold text-gray-500 mb-1">Terms & Conditions</h4>
            <p class="text-sm text-gray-600">{{ $quotation->terms_conditions }}</p>
        </div>
    @endif
</div>
@endsection
