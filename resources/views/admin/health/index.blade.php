@extends('layouts.admin')
@section('title', 'System Health')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">System Health</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Server status and diagnostics</p>
        </div>
        <div style="display:flex;gap:.375rem;">
            <form method="POST" action="/admin/health/clear-cache" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Clear all application cache?')">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Cache
                </button>
            </form>
            <form method="POST" action="/admin/health/truncate-logs" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Clear the application log file?')">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear Logs
                </button>
            </form>
        </div>
    </div>

    <div class="health-grid" style="margin-bottom:1.5rem;">
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">PHP Version</div>
            <div class="health-value">{{ $health['php_version'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Laravel Version</div>
            <div class="health-value">{{ $health['laravel_version'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Platform</div>
            <div class="health-value" style="font-size:.875rem;">{{ $health['os'] }}</div>
            <div class="cell-sub">{{ $health['server_software'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot {{ $health['db_connected'] ? 'green' : 'red' }}"></div>
            <div class="health-label">Database</div>
            <div class="health-value" style="{{ $health['db_connected'] ? 'color:var(--emerald-600);' : 'color:var(--red-600);' }}">
                {{ $health['db_connected'] ? 'Connected' : 'Disconnected' }}
            </div>
            <div class="cell-sub">{{ strtoupper($health['db_driver']) }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot {{ $health['cache_working'] ? 'green' : 'red' }}"></div>
            <div class="health-label">Cache</div>
            <div class="health-value" style="{{ $health['cache_working'] ? 'color:var(--emerald-600);' : 'color:var(--red-600);' }}">
                {{ $health['cache_working'] ? 'Working' : 'Error' }}
            </div>
            <div class="cell-sub">{{ strtoupper($health['cache_driver']) }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Queue Driver</div>
            <div class="health-value">{{ strtoupper($health['queue_driver']) }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot {{ $health['pusher_configured'] ? 'green' : 'amber' }}"></div>
            <div class="health-label">Pusher</div>
            <div class="health-value" style="{{ $health['pusher_configured'] ? 'color:var(--emerald-600);' : 'color:var(--amber-600);' }}">
                {{ $health['pusher_configured'] ? 'Configured' : 'Not Set' }}
            </div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Storage Used</div>
            <div class="health-value">{{ $health['storage_used'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Log File Size</div>
            <div class="health-value">{{ $health['log_size'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Memory Limit</div>
            <div class="health-value">{{ $health['memory_limit'] }}</div>
        </div>
        <div class="health-card">
            <div class="status-dot green"></div>
            <div class="health-label">Max Upload</div>
            <div class="health-value">{{ $health['max_upload'] }}</div>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <div class="d-card-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <svg style="width:1rem;height:1rem;color:var(--gray-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                <h3>Database Table Counts</h3>
            </div>
        </div>
        <div class="d-card-body-compact">
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
                        <td>
                            <span style="font-family:'SF Mono','Fira Code',monospace;font-size:.8125rem;color:var(--gray-600);">{{ $table }}</span>
                        </td>
                        <td style="text-align:right;">
                            <span style="font-weight:700;font-variant-numeric:tabular-nums;">{{ is_int($count) ? number_format($count) : $count }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
