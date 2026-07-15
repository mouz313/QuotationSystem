@extends('layouts.app')
@section('title', 'Quotations')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Quotations</h1>
        <p class="text-sm text-gray-500">Manage all your quotations</p>
    </div>
    <div class="flex gap-2">
        <a href="/quotations/export" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Export CSV</a>
        <a href="/quotations/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Quotation</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-4 mb-4">
    <form method="GET" class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote # or client name..."
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg text-sm outline-none">
                <option value="">All</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Declined</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                class="px-3 py-2 border rounded-lg text-sm outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                class="px-3 py-2 border rounded-lg text-sm outline-none">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
        <a href="/quotations" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Clear</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <form id="bulkForm" method="POST" action="/quotations/bulk-delete" onsubmit="return confirm('Delete selected quotations?')">
        @csrf
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 bg-gray-50">
                <th class="px-4 py-3 w-10"><input type="checkbox" id="selectAll" onchange="toggleAll()"></th>
                <th class="px-4 py-3">Quote #</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Expiry</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th>
            </tr></thead>
            <tbody>
            @forelse($quotations as $q)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $q->id }}" class="row-checkbox"></td>
                    <td class="px-4 py-3 font-medium text-indigo-600"><a href="/quotations/{{ $q->id }}" class="hover:underline">{{ $q->quote_number }}</a></td>
                    <td class="px-4 py-3 text-gray-600">{{ $q->client->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $q->issue_date->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $q->expiry_date?->format('M d, Y') ?? '-' }}</td>
                    <td class="px-4 py-3 font-medium">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($q->status) {
                                'draft' => 'bg-gray-100 text-gray-600',
                                'sent' => 'bg-blue-100 text-blue-700',
                                'opened' => 'bg-amber-100 text-amber-700',
                                'change_requested' => 'bg-purple-100 text-purple-700',
                                'accepted' => 'bg-emerald-100 text-emerald-700',
                                'declined' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $q->status)) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="/quotations/{{ $q->id }}" class="text-indigo-600 hover:underline text-xs">View</a>
                            <a href="/quotations/{{ $q->id }}/pdf" class="text-gray-500 hover:text-gray-700 text-xs">PDF</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No quotations yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        @if($quotations->count() > 0)
        <div class="px-4 py-3 border-t bg-gray-50 flex items-center gap-2">
            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete Selected</button>
        </div>
        @endif
    </form>
</div>
<div class="mt-4">{{ $quotations->links() }}</div>

<script>
function toggleAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = document.getElementById('selectAll').checked);
}
</script>
@endsection
