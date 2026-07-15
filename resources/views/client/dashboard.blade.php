@extends('client.layouts.client')
@section('title', 'Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">My Quotations</h1>
    <p class="text-sm text-gray-500">View and manage quotations from all companies</p>
</div>

<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        <p class="text-sm text-gray-500">Total</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['accepted'] }}</p>
        <p class="text-sm text-gray-500">Accepted</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        <p class="text-sm text-gray-500">Pending</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border">
        <p class="text-2xl font-bold text-red-600">{{ $stats['declined'] }}</p>
        <p class="text-sm text-gray-500">Declined</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">All Quotations</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Company</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($quotations as $q)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $q->quote_number }}</td>
                    <td class="px-4 py-3">{{ $q->user?->company?->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $q->issue_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right font-medium">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                    <td class="px-4 py-3 text-center">
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
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $q->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/client/quotations/{{ $q->id }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No quotations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $quotations->links() }}</div>
</div>
@endsection
