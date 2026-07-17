@extends('layouts.app')
@section('title', 'Reports')
@section('header-title', 'Reports & Analytics')
@section('header-sub', 'View your company performance and export data')
@section('content')
<div class="fade-in">
    <x-page-header title="Reports & Analytics" subtitle="View your company performance and export data" />

    {{-- Period Filter --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <div class="tab-group">
            @php $periods = ['this_month' => 'This Month', 'this_quarter' => 'This Quarter', 'this_year' => 'This Year', 'all_time' => 'All Time']; @endphp
            @foreach($periods as $key => $label)
                <a href="?period={{ $key }}" class="tab-pill {{ ($period ?? 'all_time') === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid" style="margin-bottom:2rem;">
        <div class="stat-card">
            <div class="stat-icon warning">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_quotations']) }}</div>
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
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['accepted_count']) }}</div>
                <div class="stat-label">Accepted</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $conversionRate }}%</div>
                <div class="stat-label">Conversion Rate</div>
            </div>
        </div>
    </div>

    {{-- Export Section --}}
    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-header">
            <div>
                <h3>Export Quotations</h3>
                <p style="font-size:.7rem;color:var(--surface-400);margin-top:.125rem;">Download your company's quotations data as CSV</p>
            </div>
        </div>
        <div class="d-card-body">
            <form method="GET" action="/reports/export" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:120px;">
                    <x-form-select name="status" label="Status" placeholder="All"
                        :options="['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined']" />
                </div>
                <div style="min-width:150px;">
                    <x-form-input label="From" name="from_date" type="date" />
                </div>
                <div style="min-width:150px;">
                    <x-form-input label="To" name="to_date" type="date" />
                </div>
                <button type="submit" class="btn btn-sm" style="background:var(--success-50);color:var(--success-600);">Download CSV</button>
            </form>
        </div>
    </div>

    {{-- Monthly Revenue Chart --}}
    <div class="d-card">
        <div class="d-card-header"><h3>Monthly Quotations & Revenue ({{ now()->year }})</h3></div>
        <div class="d-card-body"><canvas id="chartRevenue" height="200"></canvas></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const months = @json($months);
        const counts = @json($counts);
        const revenues = @json($revenues);
        const fontFamily = "'Instrument Sans', sans-serif";

        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Quotations',
                        data: counts,
                        backgroundColor: 'oklch(0.55 0.17 275)',
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Revenue',
                        data: revenues,
                        type: 'line',
                        borderColor: 'oklch(0.62 0.17 150)',
                        backgroundColor: 'oklch(0.62 0.17 150 / .08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'oklch(0.62 0.17 150)',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 11, family: fontFamily } } } },
                scales: {
                    y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Quotations', font: { size: 11 } }, ticks: { stepSize: 1, font: { family: fontFamily, size: 11 } }, grid: { color: 'oklch(0 0 0 / .04)' } },
                    y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Revenue', font: { size: 11 } }, ticks: { font: { family: fontFamily, size: 11 } }, grid: { drawOnChartArea: false } },
                    x: { ticks: { font: { family: fontFamily, size: 11 } }, grid: { display: false } }
                }
            }
        });
    });
    </script>
</div>
@endsection
