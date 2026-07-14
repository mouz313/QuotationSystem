@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Clients</div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['clients'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Items</div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['items'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Quotations</div>
        <div class="text-2xl font-bold text-indigo-600">{{ $stats['quotations'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Revenue</div>
        <div class="text-2xl font-bold text-green-600">${{ number_format($stats['revenue'], 2) }}</div>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Quotations</h2>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">#</th><th class="pb-2">Client</th><th class="pb-2">Total</th><th class="pb-2">Status</th></tr></thead>
            <tbody>
            @forelse($recentQuotations as $q)
                <tr class="border-b">
                    <td class="py-2"><a href="/quotations/{{ $q->id }}" class="text-indigo-600 hover:underline">{{ $q->quote_number }}</a></td>
                    <td class="py-2 text-gray-600">{{ $q->client->name }}</td>
                    <td class="py-2 font-medium">${{ number_format($q->grand_total, 2) }}</td>
                    <td class="py-2">
                        @if($q->status === 'draft')<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Draft</span>
                        @elseif($q->status === 'sent')<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Sent</span>
                        @elseif($q->status === 'accepted')<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Accepted</span>
                        @else<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Declined</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400">No quotations yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="/quotations/create" class="block px-4 py-3 bg-indigo-50 rounded-lg text-sm font-medium text-indigo-700 hover:bg-indigo-100">+ Create New Quotation</a>
            <a href="/clients/create" class="block px-4 py-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">+ Add New Client</a>
            <a href="/items/create" class="block px-4 py-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">+ Add New Item</a>
        </div>
    </div>
</div>
@endsection
