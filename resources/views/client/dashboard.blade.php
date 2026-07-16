@extends('client.layouts.client')
@section('title', 'Dashboard')
@section('content')

@php
    $clientUser = auth('client')->user();
    $totalQuotations = $stats['total'];
    $acceptedQuotations = $stats['accepted'];
    $pendingQuotations = $stats['pending'];
    $changeQuotations = $stats['change_req'];
    $declinedQuotations = $stats['declined'];
@endphp

<style>
    .d-card{transition:all .2s ease}
    .d-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
    .d-row:hover{background:rgba(99,102,241,.02)}
    @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    .fade-in{animation:fadeUp .35s ease-out both}
</style>

{{-- Hero --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 p-6 sm:p-8 text-white mb-6 fade-in">
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full"></div>
    <div class="absolute -bottom-16 -left-8 w-40 h-40 bg-white/5 rounded-full"></div>
    <div class="relative z-10">
        <p class="text-white/70 text-sm font-medium">Welcome back</p>
        <h1 class="text-2xl sm:text-3xl font-bold mt-1">{{ $clientUser->name }}</h1>
        <p class="text-white/60 text-sm mt-2">{{ $totalQuotations }} quotation{{ $totalQuotations !== 1 ? 's' : '' }} across {{ $clientUser->companies->count() }} {{ Str::plural('company', $clientUser->companies->count()) }}</p>
        @if($currencyTotals->count() > 0)
        <div class="flex flex-wrap gap-3 mt-5">
            @foreach($currencyTotals as $ct)
            <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 min-w-[120px]">
                <p class="text-lg font-bold">{{ $ct->symbol }}{{ number_format($ct->total_value, 0) }}</p>
                <p class="text-white/50 text-[10px] uppercase tracking-wider font-semibold">{{ $ct->code }} Total</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 min-w-[120px]">
                <p class="text-lg font-bold text-emerald-300">{{ $ct->symbol }}{{ number_format($ct->paid_amount, 0) }}</p>
                <p class="text-white/50 text-[10px] uppercase tracking-wider font-semibold">{{ $ct->code }} Paid</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6 fade-in" style="animation-delay:.05s">
    <a href="/client/dashboard" class="d-card bg-white rounded-xl p-4 border border-indigo-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $totalQuotations }}</p>
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total</p>
            </div>
        </div>
    </a>
    <div class="d-card bg-white rounded-xl p-4 border border-emerald-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-emerald-600">{{ $acceptedQuotations }}</p>
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Accepted</p>
            </div>
        </div>
    </div>
    <div class="d-card bg-white rounded-xl p-4 border border-amber-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-amber-600">{{ $pendingQuotations }}</p>
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Pending</p>
            </div>
        </div>
    </div>
    <div class="d-card bg-white rounded-xl p-4 border border-violet-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-violet-600">{{ $changeQuotations }}</p>
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Changes</p>
            </div>
        </div>
    </div>
    <div class="d-card bg-white rounded-xl p-4 border border-red-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-red-500">{{ $declinedQuotations }}</p>
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Declined</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Main Column --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Action Required --}}
        @if($actionRequired->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.1s">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Action Required</h2>
                <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full ml-1">{{ $actionRequired->count() }}</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($actionRequired as $q)
                <a href="/client/quotations/{{ $q->id }}" class="d-row flex items-center justify-between px-5 py-3 group transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-bold
                            @if($q->status === 'sent') bg-blue-500
                            @elseif($q->status === 'opened') bg-amber-500
                            @else bg-violet-500 @endif">
                            {{ strtoupper(substr($q->quote_number, -2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600 transition">{{ $q->quote_number }}</p>
                            <p class="text-[11px] text-gray-400">{{ $q->user?->company?->name ?? 'N/A' }} &middot; {{ $q->issue_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-800">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</span>
                        @php
                            $sBadge = match($q->status) {
                                'sent' => 'bg-blue-100 text-blue-700',
                                'opened' => 'bg-amber-100 text-amber-700',
                                'change_requested' => 'bg-violet-100 text-violet-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $sBadge }} uppercase tracking-wider hidden sm:inline-block">{{ str_replace('_', ' ', $q->status) }}</span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- All Quotations --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.15s">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">All Quotations</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-2.5 text-left font-semibold">Quote</th>
                            <th class="px-5 py-2.5 text-left font-semibold hidden sm:table-cell">Company</th>
                            <th class="px-5 py-2.5 text-left font-semibold hidden md:table-cell">Date</th>
                            <th class="px-5 py-2.5 text-right font-semibold">Amount</th>
                            <th class="px-5 py-2.5 text-center font-semibold">Status</th>
                            <th class="px-5 py-2.5 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($quotations as $q)
                        <tr class="d-row transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                        {{ strtoupper(substr($q->quote_number, -2)) }}
                                    </div>
                                    <div>
                                        <a href="/client/quotations/{{ $q->id }}" class="font-semibold text-gray-800 hover:text-indigo-600 transition">{{ $q->quote_number }}</a>
                                        @if($q->isMilestone())
                                            <span class="ml-1 text-[9px] font-bold px-1.5 py-0.5 rounded bg-violet-100 text-violet-600 uppercase">MS</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-500 hidden sm:table-cell">{{ $q->user?->company?->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-gray-400 text-xs hidden md:table-cell">{{ $q->issue_date->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                @php
                                    $badge = match($q->status) {
                                        'draft' => 'bg-gray-100 text-gray-500',
                                        'sent' => 'bg-blue-100 text-blue-700',
                                        'opened' => 'bg-amber-100 text-amber-700',
                                        'change_requested' => 'bg-violet-100 text-violet-700',
                                        'accepted' => 'bg-emerald-100 text-emerald-700',
                                        'declined' => 'bg-red-100 text-red-600',
                                        default => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $badge }} uppercase tracking-wider">{{ str_replace('_', ' ', $q->status) }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="/client/quotations/{{ $q->id }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">View &rarr;</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-sm text-gray-400 font-medium">No quotations yet</p>
                                    <p class="text-xs text-gray-300 mt-1">Quotations from your companies will appear here</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($quotations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $quotations->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">

        {{-- Companies --}}
        @if($clientUser->companies->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.1s">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">My Companies</h3>
            </div>
            <div class="space-y-2">
                @foreach($clientUser->companies as $company)
                <div class="flex items-center gap-3 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg, {{ $company->brand_color ?? '#4f46e5' }}, {{ $company->brand_color ?? '#7c3aed' }})">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $company->name }}</p>
                    </div>
                    <div class="w-2 h-2 rounded-full {{ $company->is_active ? 'bg-emerald-400' : 'bg-gray-300' }}"></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Payment Overview --}}
        @if($currencyTotals->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.15s">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Payments</h3>
            </div>
            @foreach($currencyTotals as $ct)
                @php
                    $paidPct = $ct->total_value > 0 ? round(($ct->paid_amount / $ct->total_value) * 100) : 0;
                    $outstanding = max(0, $ct->total_value - $ct->paid_amount);
                @endphp
                <div class="{{ !$loop->first ? 'mt-4 pt-3 border-t border-gray-100' : '' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">{{ $ct->code }}</span>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full bg-emerald-500 transition-all duration-500" style="width:{{ $paidPct }}%"></div>
                        </div>
                        <span class="text-[11px] font-bold text-gray-500">{{ $paidPct }}%</span>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total</span>
                            <span class="font-bold text-gray-700">{{ $ct->symbol }}{{ number_format($ct->total_value, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-emerald-500">Paid</span>
                            <span class="font-bold text-emerald-600">{{ $ct->symbol }}{{ number_format($ct->paid_amount, 2) }}</span>
                        </div>
                        @if($outstanding > 0)
                        <div class="flex justify-between">
                            <span class="text-red-400">Due</span>
                            <span class="font-bold text-red-500">{{ $ct->symbol }}{{ number_format($outstanding, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Recent --}}
        @if($recentQuotations->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 fade-in" style="animation-delay:.2s">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800">Recent</h3>
            </div>
            <div class="space-y-1">
                @foreach($recentQuotations as $q)
                <a href="/client/quotations/{{ $q->id }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition group">
                    <div class="w-2 h-2 rounded-full flex-shrink-0
                        @if($q->status === 'accepted') bg-emerald-400
                        @elseif($q->status === 'declined') bg-red-400
                        @elseif(in_array($q->status, ['sent','opened'])) bg-amber-400
                        @elseif($q->status === 'change_requested') bg-violet-400
                        @else bg-gray-300 @endif">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-gray-800 group-hover:text-indigo-600 transition truncate">{{ $q->quote_number }}</p>
                        <p class="text-[10px] text-gray-400">{{ $q->issue_date->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-bold text-gray-600">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 0) }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
