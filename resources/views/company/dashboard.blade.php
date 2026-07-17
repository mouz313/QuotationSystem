@extends('layouts.app')
@section('title', 'Dashboard')
@section('header-title', 'Dashboard')
@section('header-sub', 'Welcome back, ' . auth()->user()->name)
@section('content')

@if(!$hasPackage)
<div class="fixed inset-0 z-50 flex items-center justify-center" style="background:oklch(0 0 0 / .5);backdrop-filter:blur(8px);">
    <div class="d-card" style="max-width:24rem;text-align:center;padding:2.5rem;">
        <div style="width:4rem;height:4rem;margin:0 auto 1.5rem;border-radius:50%;background:var(--surface-100);display:flex;align-items:center;justify-content:center;">
            <svg style="width:2rem;height:2rem;color:var(--surface-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h2 style="font-size:1.125rem;font-weight:800;color:var(--surface-800);margin-bottom:.5rem;">No Active Package</h2>
        <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:.5rem;">Your company <strong>{{ $company->name ?? 'N/A' }}</strong> does not have an active subscription.</p>
        <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1.5rem;">Contact your admin to purchase or renew a subscription plan.</p>
        <a href="/company/settings" class="btn btn-brand" style="margin:0 auto;">Go to Settings</a>
    </div>
</div>
@endif

{{-- Period Filter --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div class="tab-group">
        @php $periods = ['this_month' => 'This Month', 'this_quarter' => 'This Quarter', 'this_year' => 'This Year', 'all_time' => 'All Time']; @endphp
        @foreach($periods as $key => $label)
            <a href="?period={{ $key }}" class="tab-pill {{ ($period ?? 'this_year') === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid fade-in">
    <div class="stat-card">
        <div class="stat-icon brand">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['clients'] }}</div>
            <div class="stat-label">Total Clients</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['items'] }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['quotations'] }}</div>
            <div class="stat-label">Total Quotations</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            @forelse($revenueByCurrency as $rev)
                <div class="stat-value" style="font-size:1.25rem;">{{ $rev->currency_symbol }}{{ number_format($rev->total, 2) }}</div>
            @empty
                <div class="stat-value" style="font-size:1.25rem;">$0.00</div>
            @endforelse
            <div class="stat-label">Revenue</div>
        </div>
    </div>
</div>

{{-- Pipeline Status --}}
@if($stats['quotations'] > 0)
@php
    $pipelineStatuses = ['draft' => 'Draft', 'sent' => 'Sent', 'opened' => 'Opened', 'change_requested' => 'Changes', 'accepted' => 'Accepted', 'declined' => 'Declined'];
    $pipelineColors = ['draft' => 'var(--surface-400)', 'sent' => 'var(--info-500)', 'opened' => 'var(--warning-500)', 'change_requested' => 'oklch(0.60 0.14 300)', 'accepted' => 'var(--success-500)', 'declined' => 'var(--danger-500)'];
@endphp
<div class="d-card fade-in fade-in-1" style="margin-top:1rem;">
    <div class="d-card-header">
        <h3>Pipeline Status</h3>
        <span style="font-size:.7rem;color:var(--surface-400);">{{ $stats['quotations'] }} total</span>
    </div>
    <div style="padding:1rem 1.25rem;">
        <div class="pipeline-bar">
            @foreach($pipelineStatuses as $status => $label)
                @if(isset($statusBreakdown[$status]) && $statusBreakdown[$status] > 0)
                    <div class="pipe-seg" style="flex:{{ $statusBreakdown[$status] }};background:{{ $pipelineColors[$status] }};" title="{{ $label }}: {{ $statusBreakdown[$status] }}"></div>
                @endif
            @endforeach
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-top:.75rem;">
            @foreach($pipelineStatuses as $status => $label)
                @if(isset($statusBreakdown[$status]) && $statusBreakdown[$status] > 0)
                    <div style="display:flex;align-items:center;gap:.375rem;font-size:.7rem;color:var(--surface-600);">
                        <span style="width:.5rem;height:.5rem;border-radius:999px;background:{{ $pipelineColors[$status] }};flex-shrink:0;"></span>
                        <span style="font-weight:600;">{{ $label }}</span>
                        <span style="color:var(--surface-400);">{{ $statusBreakdown[$status] }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Charts --}}
<div class="chart-grid-3 fade-in fade-in-2" style="margin-top:1rem;">
    <div class="d-card">
        <div class="d-card-header"><h3>Monthly Quotations ({{ now()->year }})</h3></div>
        <div class="d-card-body"><canvas id="chartQuotations" height="180"></canvas></div>
    </div>
    <div class="d-card">
        <div class="d-card-header"><h3>Monthly Revenue ({{ now()->year }})</h3></div>
        <div class="d-card-body"><canvas id="chartRevenue" height="180"></canvas></div>
    </div>
    <div class="d-card">
        <div class="d-card-header"><h3>Status Breakdown</h3></div>
        <div class="d-card-body"><canvas id="chartStatus" height="180"></canvas></div>
    </div>
</div>

{{-- Recent Quotations + Quick Actions --}}
<div class="grid-2-1 fade-in fade-in-3" style="margin-top:1rem;">
    <div class="d-card">
        <div class="d-card-header">
            <h3>Recent Quotations</h3>
            <a href="/quotations" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="d-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentQuotations as $q)
                    <tr>
                        <td><a href="/quotations/{{ $q->id }}" style="font-weight:600;color:var(--brand-600);text-decoration:none;">{{ $q->quote_number }}</a></td>
                        <td>{{ $q->client->name }}</td>
                        <td style="font-weight:700;">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                        <td><span class="badge badge-{{ $q->status }}">{{ str_replace('_', ' ', ucfirst($q->status)) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--surface-400);padding:2rem;">No quotations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-card" style="height:fit-content;">
        <div class="d-card-header"><h3>Quick Actions</h3></div>
        <div class="d-card-body" style="display:flex;flex-direction:column;gap:.5rem;">
            <a href="/quotations/create" class="qa-btn primary">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Create New Quotation
            </a>
            <a href="/clients/create" class="qa-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add New Client
            </a>
            <a href="/items/create" class="qa-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Add New Item
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const months = @json($months);
    const counts = @json($counts);
    const revenues = @json($revenues);
    const statusData = @json($statusBreakdown);
    const fontFamily = "'Instrument Sans', sans-serif";

    new Chart(document.getElementById('chartQuotations'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Quotations',
                data: counts,
                backgroundColor: 'oklch(0.55 0.17 275)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } }, x: { ticks: { font: { family: fontFamily, size: 11 } }, grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('chartRevenue'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: revenues,
                borderColor: 'oklch(0.62 0.17 150)',
                backgroundColor: 'oklch(0.62 0.17 150 / .08)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: 'oklch(0.62 0.17 150)',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } }, x: { ticks: { font: { family: fontFamily, size: 11 } }, grid: { display: false } } }
        }
    });

    const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '));
    const statusValues = Object.values(statusData);
    const statusColors = Object.keys(statusData).map(s => {
        const map = { draft: 'oklch(0.60 0.01 260)', sent: 'oklch(0.58 0.16 240)', opened: 'oklch(0.70 0.16 80)', change_requested: 'oklch(0.55 0.14 300)', accepted: 'oklch(0.62 0.17 150)', declined: 'oklch(0.60 0.20 25)' };
        return map[s] || 'oklch(0.60 0.01 260)';
    });

    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusColors,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11, family: fontFamily } } } },
            cutout: '65%',
        }
    });
});
</script>
@endsection
