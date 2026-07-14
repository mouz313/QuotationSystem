@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500">Overview of your SaaS platform</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Companies</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_companies']) }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ $stats['active_companies'] }} active</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Quotations</div>
        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_quotations']) }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ $stats['accepted_quotes'] }} accepted</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Revenue</div>
        <div class="text-2xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="text-xs text-gray-400 mt-1">${{ number_format($stats['monthly_revenue'], 2) }} this month</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Conversion Rate</div>
        <div class="text-2xl font-bold text-amber-600">{{ $stats['conversion_rate'] }}%</div>
        <div class="text-xs text-gray-400 mt-1">{{ $stats['total_users'] }} total users</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Revenue Over Time --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">Revenue (Last 12 Months)</h3>
        <canvas id="revenueChart" height="200"></canvas>
    </div>

    {{-- Quotations by Status --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">Quotations by Status</h3>
        <div class="flex justify-center"><canvas id="statusChart" height="200"></canvas></div>
    </div>

    {{-- Top Companies --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">Top Companies by Revenue</h3>
        <canvas id="topCompaniesChart" height="200"></canvas>
    </div>

    {{-- Company Growth --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">New Companies (Last 12 Months)</h3>
        <canvas id="growthChart" height="200"></canvas>
    </div>
</div>

{{-- Recent Activity --}}
@if($recentActivity->count())
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
    <div class="space-y-3">
        @foreach($recentActivity as $log)
            <div class="flex items-center gap-3 text-sm">
                <span class="px-2 py-1 text-xs rounded-full {{ \App\Models\ActivityLog::getActionColor($log->action) }}">
                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                </span>
                <span class="text-gray-600 flex-1">{{ $log->description ?? $log->subject_type }}</span>
                <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Companies --}}
<div class="bg-white rounded-xl shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Recent Companies</h3>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 border-b">
            <th class="pb-2">Name</th><th class="pb-2">Email</th><th class="pb-2">Status</th><th class="pb-2">Package</th><th class="pb-2">Users</th><th class="pb-2">Created</th>
        </tr></thead>
        <tbody>
        @forelse($recentCompanies as $company)
            <tr class="border-b">
                <td class="py-2"><a href="/admin/companies/{{ $company->id }}" class="text-indigo-600 hover:underline">{{ $company->name }}</a></td>
                <td class="py-2 text-gray-600">{{ $company->email }}</td>
                <td class="py-2">
                    @if($company->status === 'active')<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @elseif($company->status === 'blocked')<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                    @else<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                    @endif
                </td>
                <td class="py-2 text-gray-600">{{ $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first()?->package?->name ?? 'None' }}</td>
                <td class="py-2 text-gray-600">{{ $company->users_count }}</td>
                <td class="py-2 text-gray-500">{{ $company->created_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-4 text-center text-gray-400">No companies yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
const chartDefaults = {
    responsive: true,
    plugins: { legend: { labels: { usePointStyle: true, padding: 15 } } },
};

// Revenue Line Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueLabels) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode($revenueData) !!},
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        }]
    },
    options: { ...chartDefaults, scales: { y: { beginAtZero: true } } }
});

// Status Doughnut
const statusColors = { draft: '#9ca3af', sent: '#3b82f6', accepted: '#22c55e', declined: '#ef4444' };
const statusData = {!! json_encode($statusCounts) !!};
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: Object.keys(statusData).map(s => statusColors[s] || '#6b7280'),
        }]
    },
    options: chartDefaults
});

// Top Companies Bar
const topComp = {!! json_encode($topCompanies->pluck('total', 'name')->toArray()) !!};
new Chart(document.getElementById('topCompaniesChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(topComp),
        datasets: [{
            label: 'Revenue ($)',
            data: Object.values(topComp),
            backgroundColor: '#818cf8',
        }]
    },
    options: { ...chartDefaults, indexAxis: 'y', scales: { x: { beginAtZero: true } } }
});

// Company Growth Line
new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($growthLabels) !!},
        datasets: [{
            label: 'New Companies',
            data: {!! json_encode($growthData) !!},
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        }]
    },
    options: { ...chartDefaults, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endsection
