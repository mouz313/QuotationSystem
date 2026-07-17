@extends('layouts.admin')
@section('title', 'Activity Log')
@section('content')
<div class="fade-in">
    <x-page-header title="Activity Log" subtitle="Track all system actions and changes">
        @slot('actions')
            <a href="{{ route('admin.activity-log.export', request()->query()) }}" class="btn btn-sm" style="background:var(--success-50);color:var(--success-600);">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        @endslot
    </x-page-header>

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <form method="GET" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <x-form-input label="Search" name="search" value="{{ request('search') }}" placeholder="Search description..." />
                </div>
                <div style="min-width:160px;">
                    <x-form-select name="action" label="Action" value="{{ request('action') }}" placeholder="All Actions"
                        :options="['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'status_changed' => 'Status Changed', 'login' => 'Login', 'package_assigned' => 'Package Assigned']" />
                </div>
                <div style="min-width:160px;">
                    <x-form-select name="user_id" label="User" value="{{ request('user_id') }}" placeholder="All Users"
                        :options="$users->pluck('name', 'id')->toArray()" />
                </div>
                <button class="btn btn-brand">Filter</button>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td style="color:var(--surface-500);font-size:.75rem;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td style="font-weight:600;">{{ $log->user?->name ?? 'System' }}</td>
                    <td>
                        <span class="badge {{ \App\Models\ActivityLog::getActionColor($log->action) }}">
                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td style="color:var(--surface-600);">{{ $log->description ?? '-' }}</td>
                    <td style="font-size:.75rem;color:var(--surface-400);">{{ $log->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <x-empty-state title="No activity recorded yet" description="Actions will appear here as they happen." icon="info" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $logs->links() }}</div>
</div>
@endsection
