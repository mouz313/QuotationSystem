@extends('layouts.admin')
@section('title', 'All Quotations')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Quotation Oversight</h1>
    <p class="text-sm text-gray-500">View all quotations across every company</p>
</div>
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" class="flex gap-3 items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote # or client name..."
                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg text-sm outline-none">
                <option value="">All</option>
                @foreach(['draft','sent','accepted','declined'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Company</label>
            <select name="company_id" class="px-3 py-2 border rounded-lg text-sm outline-none">
                <option value="">All</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-3 py-2 border rounded-lg text-sm outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-3 py-2 border rounded-lg text-sm outline-none">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Quote #</th><th class="px-4 py-3">Company</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($quotations as $q)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-indigo-600">{{ $q->quote_number }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $q->user->company?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $q->client->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $q->issue_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                <td class="px-4 py-3">
                    @if($q->status === 'draft')<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Draft</span>
                    @elseif($q->status === 'sent')<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Sent</span>
                    @elseif($q->status === 'accepted')<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Accepted</span>
                    @else<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Declined</span>
                    @endif
                </td>
                <td class="px-4 py-3"><a href="/admin/quotations/{{ $q->id }}" class="text-indigo-600 hover:underline text-xs">View</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No quotations found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $quotations->links() }}</div>
@endsection
