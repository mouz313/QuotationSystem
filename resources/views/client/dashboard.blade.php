@extends('client.layouts.client')
@section('title', 'Dashboard')
@section('header-title', 'Client Portal')
@section('header-sub', 'Welcome back, ' . auth('client')->user()->name)
@section('content')

@php
    $clientUser = auth('client')->user();
    $totalQuotations = $stats['total'];
    $acceptedQuotations = $stats['accepted'];
    $pendingQuotations = $stats['pending'];
    $changeQuotations = $stats['change_req'];
    $declinedQuotations = $stats['declined'];
@endphp

{{-- Hero --}}
<div class="fade-in" style="border-radius:.75rem;background:linear-gradient(135deg, var(--brand-700), var(--brand-500), oklch(0.50 0.14 300));padding:2rem;color:white;position:relative;overflow:hidden;margin-bottom:1rem;">
    <div style="position:absolute;top:-3rem;right:-3rem;width:12rem;height:12rem;background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-4rem;left:-2rem;width:10rem;height:10rem;background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <div style="position:relative;z-index:1;">
        <p style="color:rgba(255,255,255,.6);font-size:.75rem;font-weight:600;">Welcome back</p>
        <h1 style="font-size:1.75rem;font-weight:800;margin-top:.25rem;">{{ $clientUser->name }}</h1>
        <p style="color:rgba(255,255,255,.5);font-size:.8125rem;margin-top:.5rem;">{{ $totalQuotations }} quotation{{ $totalQuotations !== 1 ? 's' : '' }} across {{ $clientUser->companies->count() }} {{ Str::plural('company', $clientUser->companies->count()) }}</p>
        @if($currencyTotals->count() > 0)
        <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1rem;">
            @foreach($currencyTotals as $ct)
            <div style="background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:.625rem;padding:.75rem 1rem;min-width:100px;">
                <p style="font-size:1.125rem;font-weight:800;">{{ $ct->symbol }}{{ number_format($ct->total_value, 0) }}</p>
                <p style="color:rgba(255,255,255,.45);font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ $ct->code }} Total</p>
            </div>
            <div style="background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:.625rem;padding:.75rem 1rem;min-width:100px;">
                <p style="font-size:1.125rem;font-weight:800;color:oklch(0.80 0.12 150);">{{ $ct->symbol }}{{ number_format($ct->paid_amount, 0) }}</p>
                <p style="color:rgba(255,255,255,.45);font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ $ct->code }} Paid</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid stat-grid-5 fade-in fade-in-1" style="margin-bottom:1rem;">
    <a href="/client/dashboard" class="stat-card" style="text-decoration:none;border-color:var(--brand-200);">
        <div class="stat-icon brand">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $totalQuotations }}</div>
            <div class="stat-label">Total</div>
        </div>
    </a>
    <div class="stat-card" style="border-color:oklch(0.88 0.04 150);">
        <div class="stat-icon success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--success-600);">{{ $acceptedQuotations }}</div>
            <div class="stat-label">Accepted</div>
        </div>
    </div>
    <div class="stat-card" style="border-color:oklch(0.88 0.06 80);">
        <div class="stat-icon warning">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--warning-600);">{{ $pendingQuotations }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="stat-card" style="border-color:oklch(0.88 0.04 300);">
        <div class="stat-icon" style="background:oklch(0.95 0.04 300);color:oklch(0.50 0.16 300);">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:oklch(0.50 0.16 300);">{{ $changeQuotations }}</div>
            <div class="stat-label">Changes</div>
        </div>
    </div>
    <div class="stat-card" style="border-color:oklch(0.88 0.04 25);">
        <div class="stat-icon danger">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--danger-600);">{{ $declinedQuotations }}</div>
            <div class="stat-label">Declined</div>
        </div>
    </div>
</div>

{{-- Pipeline --}}
@if($totalQuotations > 0)
@php
    $pipelineColors = ['draft' => 'var(--surface-400)', 'sent' => 'var(--info-500)', 'opened' => 'var(--warning-500)', 'change_requested' => 'oklch(0.55 0.14 300)', 'accepted' => 'var(--success-500)', 'declined' => 'var(--danger-500)'];
    $pipelineCounts = ['draft' => 0, 'sent' => 0, 'opened' => 0, 'change_requested' => 0, 'accepted' => $acceptedQuotations, 'declined' => $declinedQuotations];
@endphp
<div class="d-card fade-in fade-in-1" style="margin-bottom:1rem;">
    <div class="d-card-header">
        <h3>Pipeline</h3>
        <span style="font-size:.7rem;color:var(--surface-400);">{{ $totalQuotations }} total</span>
    </div>
    <div style="padding:1rem 1.25rem;">
        <div class="pipeline-bar">
            @foreach($pipelineCounts as $status => $count)
                @if($count > 0)
                    <div class="pipe-seg" style="flex:{{ $count }};background:{{ $pipelineColors[$status] }};"></div>
                @endif
            @endforeach
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-top:.75rem;">
            @foreach($pipelineCounts as $status => $count)
                @if($count > 0)
                    <div style="display:flex;align-items:center;gap:.375rem;font-size:.7rem;color:var(--surface-600);">
                        <span style="width:.5rem;height:.5rem;border-radius:999px;background:{{ $pipelineColors[$status] }};flex-shrink:0;"></span>
                        <span style="font-weight:600;">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        <span style="color:var(--surface-400);">{{ $count }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="grid-2-1 fade-in fade-in-2">

    {{-- Main Column --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Action Required --}}
        @if($actionRequired->count() > 0)
        <div class="d-card">
            <div class="d-card-header" style="border-bottom-color:oklch(0.88 0.06 80);">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="stat-icon warning" style="width:1.75rem;height:1.75rem;">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <h3>Action Required</h3>
                </div>
                <span class="badge badge-opened">{{ $actionRequired->count() }}</span>
            </div>
            <div style="padding:0;">
                @foreach($actionRequired as $q)
                <a href="/client/quotations/{{ $q->id }}" style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;text-decoration:none;color:var(--surface-700);border-bottom:1px solid var(--surface-100);transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background=''">
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <div style="width:2rem;height:2rem;border-radius:.375rem;display:flex;align-items:center;justify-content:center;color:white;font-size:.65rem;font-weight:800;flex-shrink:0;background:{{ match($q->status) { 'sent' => 'var(--info-500)', 'opened' => 'var(--warning-500)', default => 'oklch(0.55 0.14 300)' } }};">
                            {{ strtoupper(substr($q->quote_number, -2)) }}
                        </div>
                        <div>
                            <p style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">{{ $q->quote_number }}</p>
                            <p style="font-size:.6875rem;color:var(--surface-400);">{{ $q->user?->company?->name ?? 'N/A' }} &middot; {{ $q->issue_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</span>
                        <span class="badge badge-{{ $q->status }}">{{ str_replace('_', ' ', $q->status) }}</span>
                        <svg style="width:1rem;height:1rem;color:var(--surface-300);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- All Quotations --}}
        <div class="d-card">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="stat-icon brand" style="width:1.75rem;height:1.75rem;">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3>All Quotations</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="d-table">
                    <thead>
                        <tr>
                            <th>Quote</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th style="text-align:right;">Amount</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:right;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $q)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <div style="width:1.75rem;height:1.75rem;border-radius:.375rem;background:linear-gradient(135deg, var(--brand-500), oklch(0.50 0.14 300));display:flex;align-items:center;justify-content:center;color:white;font-size:.55rem;font-weight:800;flex-shrink:0;">
                                        {{ strtoupper(substr($q->quote_number, -2)) }}
                                    </div>
                                    <a href="/client/quotations/{{ $q->id }}" style="font-weight:700;color:var(--surface-800);text-decoration:none;font-size:.8125rem;">{{ $q->quote_number }}</a>
                                    @if($q->isMilestone())
                                        <span class="badge" style="background:oklch(0.95 0.04 300);color:oklch(0.50 0.16 300);padding:.1rem .35rem;font-size:.5rem;">MS</span>
                                    @endif
                                </div>
                            </td>
                            <td style="color:var(--surface-500);">{{ $q->user?->company?->name ?? 'N/A' }}</td>
                            <td style="color:var(--surface-400);font-size:.75rem;">{{ $q->issue_date->format('d M Y') }}</td>
                            <td style="text-align:right;font-weight:700;color:var(--surface-800);">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                            <td style="text-align:center;"><span class="badge badge-{{ $q->status }}">{{ str_replace('_', ' ', $q->status) }}</span></td>
                            <td style="text-align:right;"><a href="/client/quotations/{{ $q->id }}" class="btn btn-ghost btn-icon" title="View" style="color:var(--brand-600);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3rem;">
                                <div style="width:3rem;height:3rem;margin:0 auto;border-radius:.625rem;background:var(--surface-100);display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;">
                                    <svg style="width:1.5rem;height:1.5rem;color:var(--surface-300);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p style="font-size:.8125rem;color:var(--surface-400);font-weight:600;">No quotations yet</p>
                                <p style="font-size:.75rem;color:var(--surface-300);margin-top:.25rem;">Quotations from your companies will appear here</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($quotations->hasPages())
            <div style="padding:.75rem 1.25rem;border-top:1px solid var(--surface-100);">
                {{ $quotations->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Companies --}}
        @if($clientUser->companies->count() > 0)
        <div class="d-card">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="stat-icon info" style="width:1.75rem;height:1.75rem;">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3>My Companies</h3>
                </div>
            </div>
            <div style="padding:.5rem 1rem;">
                @foreach($clientUser->companies as $company)
                <div style="display:flex;align-items:center;gap:.75rem;padding:.625rem;border-radius:.5rem;{{ !$loop->last ? 'margin-bottom:.25rem;' : '' }}">
                    <div style="width:2rem;height:2rem;border-radius:.375rem;display:flex;align-items:center;justify-content:center;color:white;font-size:.6rem;font-weight:800;flex-shrink:0;background:linear-gradient(135deg, {{ $company->brand_color ?? 'var(--brand-600)' }}, {{ $company->brand_color ?? 'oklch(0.50 0.14 300)' }});">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.8125rem;font-weight:700;color:var(--surface-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $company->name }}</p>
                    </div>
                    <div style="width:.375rem;height:.375rem;border-radius:999px;background:{{ $company->isActive() ? 'var(--success-500)' : 'var(--surface-300)' }};flex-shrink:0;"></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Payment Overview --}}
        @if($currencyTotals->count() > 0)
        <div class="d-card">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="stat-icon success" style="width:1.75rem;height:1.75rem;">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3>Payments</h3>
                </div>
            </div>
            <div style="padding:1rem 1.25rem;">
                @foreach($currencyTotals as $ct)
                    @php
                        $paidPct = $ct->total_value > 0 ? round(($ct->paid_amount / $ct->total_value) * 100) : 0;
                        $outstanding = max(0, $ct->total_value - $ct->paid_amount);
                    @endphp
                    <div style="{{ !$loop->first ? 'margin-top:1rem;padding-top:.75rem;border-top:1px solid var(--surface-100);' : '' }}">
                        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                            <span style="font-size:.7rem;font-weight:800;color:var(--surface-600);text-transform:uppercase;letter-spacing:.04em;">{{ $ct->code }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                            <div style="flex:1;background:var(--surface-100);border-radius:999px;height:.375rem;">
                                <div style="height:.375rem;border-radius:999px;background:var(--success-500);width:{{ $paidPct }}%;transition:width .4s;"></div>
                            </div>
                            <span style="font-size:.6875rem;font-weight:700;color:var(--surface-500);">{{ $paidPct }}%</span>
                        </div>
                        <div style="font-size:.75rem;display:flex;flex-direction:column;gap:.125rem;">
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:var(--surface-400);">Total</span>
                                <span style="font-weight:700;color:var(--surface-600);">{{ $ct->symbol }}{{ number_format($ct->total_value, 2) }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:var(--success-500);">Paid</span>
                                <span style="font-weight:700;color:var(--success-600);">{{ $ct->symbol }}{{ number_format($ct->paid_amount, 2) }}</span>
                            </div>
                            @if($outstanding > 0)
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:var(--danger-500);">Due</span>
                                <span style="font-weight:700;color:var(--danger-600);">{{ $ct->symbol }}{{ number_format($outstanding, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent --}}
        @if($recentQuotations->count() > 0)
        <div class="d-card">
            <div class="d-card-header">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="stat-icon" style="width:1.75rem;height:1.75rem;background:var(--surface-100);color:var(--surface-500);">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3>Recent</h3>
                </div>
            </div>
            <div style="padding:.25rem 1rem;">
                @foreach($recentQuotations as $q)
                <a href="/client/quotations/{{ $q->id }}" style="display:flex;align-items:center;gap:.75rem;padding:.5rem;border-radius:.375rem;text-decoration:none;color:var(--surface-700);transition:background .15s;" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background=''">
                    <span style="width:.375rem;height:.375rem;border-radius:999px;flex-shrink:0;background:{{ match($q->status) { 'accepted' => 'var(--success-500)', 'declined' => 'var(--danger-500)', 'sent', 'opened' => 'var(--warning-500)', 'change_requested' => 'oklch(0.55 0.14 300)', default => 'var(--surface-300)' } }};"></span>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.75rem;font-weight:700;color:var(--surface-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $q->quote_number }}</p>
                        <p style="font-size:.625rem;color:var(--surface-400);">{{ $q->issue_date->diffForHumans() }}</p>
                    </div>
                    <span style="font-size:.75rem;font-weight:700;color:var(--surface-600);">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 0) }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
