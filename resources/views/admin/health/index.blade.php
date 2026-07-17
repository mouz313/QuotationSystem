@extends('layouts.admin')
@section('title', 'System Health')
@section('content')
<div class="fade-in">
    <x-page-header title="System Health" subtitle="Server status and diagnostics">
        @slot('actions')
            <form method="POST" action="/admin/health/clear-cache" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:var(--warning-50);color:var(--warning-600);" onclick="return confirm('Clear all application cache?')">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Cache
                </button>
            </form>
            <form method="POST" action="/admin/health/truncate-logs" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:var(--surface-100);color:var(--surface-600);" onclick="return confirm('Clear the application log file?')">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Logs
                </button>
            </form>
        @endslot
    </x-page-header>

    <div class="stat-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card">
            <div>
                <div class="stat-label">PHP Version</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['php_version'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Laravel Version</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['laravel_version'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Platform</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['os'] }} {{ $health['server_software'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Database</div>
                <div class="stat-value" style="font-size:1.25rem;{{ $health['db_connected'] ? 'color:var(--success-600);' : 'color:var(--danger-600);' }}">
                    {{ $health['db_connected'] ? 'Connected' : 'Disconnected' }}
                </div>
                <div class="stat-sub">{{ strtoupper($health['db_driver']) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Cache</div>
                <div class="stat-value" style="font-size:1.25rem;{{ $health['cache_working'] ? 'color:var(--success-600);' : 'color:var(--danger-600);' }}">
                    {{ $health['cache_working'] ? 'Working' : 'Error' }}
                </div>
                <div class="stat-sub">{{ strtoupper($health['cache_driver']) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Queue Driver</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ strtoupper($health['queue_driver']) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Pusher Configured</div>
                <div class="stat-value" style="font-size:1.25rem;{{ $health['pusher_configured'] ? 'color:var(--success-600);' : 'color:var(--warning-600);' }}">
                    {{ $health['pusher_configured'] ? 'Yes' : 'No' }}
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Storage Used</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['storage_used'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Log File Size</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['log_size'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Memory Limit</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['memory_limit'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Max Upload Size</div>
                <div class="stat-value" style="font-size:1.25rem;">{{ $health['max_upload'] }}</div>
            </div>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <div class="d-card-header">
            <h3>Database Table Counts</h3>
        </div>
        <table class="d-table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th style="text-align:right;">Rows</th>
                </tr>
            </thead>
            <tbody>
            @foreach($health['table_counts'] as $table => $count)
                <tr>
                    <td style="font-family:monospace;font-size:.8125rem;">{{ $table }}</td>
                    <td style="text-align:right;font-weight:600;">{{ is_int($count) ? number_format($count) : $count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
