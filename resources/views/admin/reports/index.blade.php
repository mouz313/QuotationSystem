@extends('layouts.admin')
@section('title', 'Reports & Exports')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Reports & Exports</h1>
    <p class="text-sm text-gray-500">Generate and download reports in PDF or Excel format</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <div class="text-3xl font-bold text-indigo-600 mb-1">{{ number_format($stats['total_companies']) }}</div>
        <div class="text-sm text-gray-500">Total Companies</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <div class="text-3xl font-bold text-indigo-600 mb-1">{{ number_format($stats['total_quotations']) }}</div>
        <div class="text-sm text-gray-500">Total Quotations</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-1">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="text-sm text-gray-500">Total Revenue</div>
    </div>
</div>

<div class="space-y-6">
    {{-- Companies Report --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold">Companies Report</h2>
                <p class="text-sm text-gray-500">All companies with packages and status</p>
            </div>
            <div class="flex gap-2">
                <a href="/admin/reports/companies/export?format=pdf" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">PDF</a>
                <a href="/admin/reports/companies/export?format=xlsx" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">CSV</a>
            </div>
        </div>
    </div>

    {{-- Quotations Report --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold">Quotations Report</h2>
                <p class="text-sm text-gray-500">Filter by status and date range</p>
            </div>
        </div>
        <form method="GET" action="/admin/reports/quotations/export" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border rounded-lg text-sm outline-none">
                    <option value="">All</option>
                    @foreach(['draft','sent','accepted','declined'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="from_date" class="px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">To</label>
                <input type="date" name="to_date" class="px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
            <button type="submit" name="format" value="pdf" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">PDF</button>
            <button type="submit" name="format" value="xlsx" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">CSV</button>
        </form>
    </div>

    {{-- Revenue Report --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold">Revenue Report</h2>
                <p class="text-sm text-gray-500">Monthly revenue from accepted quotations</p>
            </div>
        </div>
        <form method="GET" action="/admin/reports/revenue/export" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="from_date" class="px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">To</label>
                <input type="date" name="to_date" class="px-3 py-2 border rounded-lg text-sm outline-none">
            </div>
            <button type="submit" name="format" value="pdf" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">PDF</button>
            <button type="submit" name="format" value="xlsx" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">CSV</button>
        </form>
    </div>
</div>
@endsection
