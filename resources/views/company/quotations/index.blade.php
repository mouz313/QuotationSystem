@extends('layouts.app')
@section('title', 'Quotations')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Quotations</h1>
        <p class="text-sm text-gray-500">Manage all your quotations</p>
    </div>
    <a href="/quotations/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Quotation</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Quote #</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Expiry</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($quotations as $q)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-indigo-600"><a href="/quotations/{{ $q->id }}" class="hover:underline">{{ $q->quote_number }}</a></td>
                <td class="px-4 py-3 text-gray-600">{{ $q->client->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $q->issue_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $q->expiry_date?->format('M d, Y') ?? '-' }}</td>
                <td class="px-4 py-3 font-medium">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                <td class="px-4 py-3">
                    @if($q->status === 'draft')<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Draft</span>
                    @elseif($q->status === 'sent')<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Sent</span>
                    @elseif($q->status === 'accepted')<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Accepted</span>
                    @else<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Declined</span>
                    @endif
                </td>
                <td class="px-4 py-3"><a href="/quotations/{{ $q->id }}" class="text-indigo-600 hover:underline text-xs">View</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No quotations yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $quotations->links() }}</div>
@endsection
