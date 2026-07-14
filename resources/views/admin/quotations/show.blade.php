@extends('layouts.admin')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $quotation->quote_number }}</h1>
        <p class="text-sm text-gray-500">Issued {{ $quotation->issue_date->format('M d, Y') }} &middot; {{ $quotation->user->company?->name ?? 'N/A' }}</p>
    </div>
    <a href="/admin/quotations" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Back</a>
</div>
<div class="flex items-center gap-2 mb-6">
    <form method="POST" action="/admin/quotations/{{ $quotation->id }}/status" class="flex items-center gap-2">
        @csrf @method('PATCH')
        <select name="status" class="text-sm border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="draft" {{ $quotation->status === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="sent" {{ $quotation->status === 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="accepted" {{ $quotation->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="declined" {{ $quotation->status === 'declined' ? 'selected' : '' }}>Declined</option>
        </select>
        <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update Status</button>
    </form>
    <a href="/admin/quotations/{{ $quotation->id }}/pdf" class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        PDF
    </a>
    <form method="POST" action="/admin/quotations/{{ $quotation->id }}" onsubmit="return confirm('Delete this quotation permanently?')" class="ml-auto">
        @csrf @method('DELETE')
        <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>
    </form>
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
            @if($quotation->currency)<div class="text-sm text-gray-500 mt-1">Currency: {{ $quotation->currency->symbol }} {{ $quotation->currency->code }}</div>@endif
            <div class="text-sm text-gray-500 mt-1">Created by: {{ $quotation->user->name }}</div>
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
            @if($quotation->discount_amount > 0)<div class="flex justify-between"><span class="text-gray-500">Discount:</span><span class="text-red-600">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>@endif
            @if($quotation->tax_percentage > 0)<div class="flex justify-between"><span class="text-gray-500">Tax ({{ $quotation->tax_percentage }}%):</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>@endif
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
