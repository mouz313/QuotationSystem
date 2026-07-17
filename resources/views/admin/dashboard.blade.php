@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('header-title', 'Dashboard')
@section('header-sub', 'Overview of your SaaS platform')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

{{-- Stat Cards --}}
<div class="stat-grid fade-in">
    <div class="stat-card">
        <div class="stat-icon brand">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_companies']) }}</div>
            <div class="stat-label">Total Companies</div>
            <div class="stat-sub">{{ $stats['active_companies'] }} active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_quotations']) }}</div>
            <div class="stat-label">Total Quotations</div>
            <div class="stat-sub">{{ $stats['accepted_quotes'] }} accepted</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">Total Revenue (all currencies)</div>
            <div class="stat-sub">{{ number_format($stats['monthly_revenue'], 2) }} this month</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['conversion_rate'] }}%</div>
            <div class="stat-label">Conversion Rate</div>
            <div class="stat-sub">{{ $stats['total_users'] }} total users</div>
        </div>
    </div>
</div>

{{-- Pipeline Status --}}
@php
    $totalQ = $stats['total_quotations'];
    $pipelineColors = ['draft' => 'var(--surface-400)', 'sent' => 'var(--info-500)', 'opened' => 'var(--warning-500)', 'accepted' => 'var(--success-500)', 'declined' => 'var(--danger-500)'];
@endphp
@if($totalQ > 0)
<div class="d-card fade-in fade-in-1" style="margin-top:1rem;">
    <div class="d-card-header">
        <h3>Quotation Pipeline</h3>
        <span style="font-size:.7rem;color:var(--surface-400);">{{ $totalQ }} total quotations</span>
    </div>
    <div style="padding:1rem 1.25rem;">
        <div class="pipeline-bar">
            @foreach($statusCounts as $status => $count)
                @if($count > 0)
                    <div class="pipe-seg" style="flex:{{ $count }};background:{{ $pipelineColors[$status] ?? 'var(--surface-400)' }};" title="{{ ucfirst($status) }}: {{ $count }}"></div>
                @endif
            @endforeach
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-top:.75rem;">
            @foreach($statusCounts as $status => $count)
                <div style="display:flex;align-items:center;gap:.375rem;font-size:.7rem;color:var(--surface-600);">
                    <span style="width:.5rem;height:.5rem;border-radius:999px;background:{{ $pipelineColors[$status] ?? 'var(--surface-400)' }};flex-shrink:0;"></span>
                    <span style="font-weight:600;">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    <span style="color:var(--surface-400);">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Charts --}}
<div class="chart-grid-2 fade-in fade-in-2" style="margin-top:1rem;">
    <div class="d-card">
        <div class="d-card-header"><h3>Revenue (Last 12 Months)</h3></div>
        <div class="d-card-body"><canvas id="revenueChart" height="180"></canvas></div>
    </div>
    <div class="d-card">
        <div class="d-card-header"><h3>Quotations by Status</h3></div>
        <div class="d-card-body" style="display:flex;justify-content:center;"><canvas id="statusChart" height="180"></canvas></div>
    </div>
    <div class="d-card">
        <div class="d-card-header"><h3>Top Companies by Revenue</h3></div>
        <div class="d-card-body"><canvas id="topCompaniesChart" height="180"></canvas></div>
    </div>
    <div class="d-card">
        <div class="d-card-header"><h3>New Companies (Last 12 Months)</h3></div>
        <div class="d-card-body"><canvas id="growthChart" height="180"></canvas></div>
    </div>
</div>

{{-- Recent Activity + Companies --}}
<div class="grid-1-2 fade-in fade-in-3" style="margin-top:1rem;">
    @if($recentActivity->count())
    <div class="d-card">
        <div class="d-card-header"><h3>Recent Activity</h3></div>
        <div style="padding:.5rem 1.25rem;">
            @foreach($recentActivity as $log)
                <div class="activity-item">
                    <span class="activity-dot" style="background:{{ match(true) {
                        str_contains($log->action, 'create') => 'var(--success-500)',
                        str_contains($log->action, 'delete') => 'var(--danger-500)',
                        str_contains($log->action, 'update') => 'var(--warning-500)',
                        default => 'var(--info-500)',
                    } }}"></span>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:.75rem;font-weight:600;color:var(--surface-700);">{{ ucwords(str_replace('_', ' ', $log->action)) }}</p>
                        <p style="font-size:.7rem;color:var(--surface-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->description ?? $log->subject_type }}</p>
                        <p style="font-size:.625rem;color:var(--surface-400);margin-top:.125rem;">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="d-card">
        <div class="d-card-header">
            <h3>Recent Companies</h3>
            <a href="/admin/companies" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="d-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Package</th>
                        <th>Users</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCompanies as $company)
                    <tr>
                        <td><a href="/admin/companies/{{ $company->id }}" style="font-weight:600;color:var(--brand-600);text-decoration:none;">{{ $company->name }}</a></td>
                        <td>{{ $company->email }}</td>
                        <td><span class="badge badge-{{ $company->status }}">{{ ucfirst($company->status) }}</span></td>
                        <td style="color:var(--surface-500);">{{ $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first()?->package?->name ?? 'None' }}</td>
                        <td style="color:var(--surface-500);">{{ $company->users_count }}</td>
                        <td style="color:var(--surface-400);font-size:.75rem;">{{ $company->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--surface-400);padding:2rem;">No companies yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const fontFamily = "'Instrument Sans', sans-serif";
const chartDefaults = {
    responsive: true,
    plugins: { legend: { labels: { usePointStyle: true, padding: 15, font: { family: fontFamily, size: 11 } } } },
};

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueLabels) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($revenueData) !!},
            borderColor: 'oklch(0.55 0.17 275)',
            backgroundColor: 'oklch(0.55 0.17 275 / .08)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: 'oklch(0.55 0.17 275)',
        }]
    },
    options: { ...chartDefaults, scales: { y: { beginAtZero: true, ticks: { font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } }, x: { ticks: { font: { family: fontFamily, size: 10 }, maxRotation: 45 }, grid: { display: false } } } }
});

const statusColors = { draft: 'oklch(0.60 0.01 260)', sent: 'oklch(0.58 0.16 240)', accepted: 'oklch(0.62 0.17 150)', declined: 'oklch(0.60 0.20 25)' };
const statusData = {!! json_encode($statusCounts) !!};
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: Object.keys(statusData).map(s => statusColors[s] || 'oklch(0.60 0.01 260)'),
        }]
    },
    options: { ...chartDefaults, cutout: '65%' }
});

const topComp = {!! json_encode($topCompanies->pluck('total', 'name')->toArray()) !!};
new Chart(document.getElementById('topCompaniesChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(topComp),
        datasets: [{
            label: 'Revenue',
            data: Object.values(topComp),
            backgroundColor: 'oklch(0.60 0.14 280)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: { ...chartDefaults, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } }, y: { ticks: { font: { family: fontFamily, size: 11 } }, grid: { display: false } } } }
});

new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($growthLabels) !!},
        datasets: [{
            label: 'New Companies',
            data: {!! json_encode($growthData) !!},
            borderColor: 'oklch(0.62 0.17 150)',
            backgroundColor: 'oklch(0.62 0.17 150 / .08)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: 'oklch(0.62 0.17 150)',
        }]
    },
    options: { ...chartDefaults, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } }, x: { ticks: { font: { family: fontFamily, size: 10 }, maxRotation: 45 }, grid: { display: false } } } }
});
</script>
@endsection
