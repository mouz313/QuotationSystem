@extends('layouts.admin')
@section('title', 'Activity Log')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Activity Log</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Track all system actions and changes</p>
        </div>
        <a href="{{ route('admin.activity-log.export', request()->query()) }}" class="btn btn-outline btn-sm">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>

    <div class="d-card" style="margin-bottom:1rem;">
        <div class="d-card-body" style="padding:.75rem 1.25rem;">
            <form method="GET" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                <div class="search-input" style="flex:1;min-width:200px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description...">
                </div>
                <select name="action" class="form-select" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="status_changed" {{ request('action') === 'status_changed' ? 'selected' : '' }}>Status Changed</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="package_assigned" {{ request('action') === 'package_assigned' ? 'selected' : '' }}>Package Assigned</option>
                </select>
                <select name="user_id" class="form-select" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-brand btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <div class="d-card">
        <div class="d-card-body" style="padding:1.25rem;">
            <div class="timeline">
                @forelse($logs as $log)
                    @php
                        $actionClass = match(true) {
                            str_contains($log->action, 'created') => 'create',
                            str_contains($log->action, 'updated') || str_contains($log->action, 'status') => 'update',
                            str_contains($log->action, 'deleted') => 'delete',
                            default => 'security',
                        };
                    @endphp
                    <div class="timeline-item {{ $actionClass }}">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                            <div>
                                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
                                    <span style="font-weight:600;color:var(--gray-900);font-size:.8125rem;">{{ $log->user?->name ?? 'System' }}</span>
                                    <span class="badge {{ \App\Models\ActivityLog::getActionColor($log->action) }}" style="font-size:.6rem;">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                </div>
                                @if($log->description)
                                    <p style="font-size:.8125rem;color:var(--gray-600);line-height:1.5;">{{ $log->description }}</p>
                                @endif
                                <div style="display:flex;align-items:center;gap:.75rem;margin-top:.25rem;">
                                    <span style="font-size:.6875rem;color:var(--gray-400);">{{ $log->created_at->format('M d, Y H:i') }}</span>
                                    @if($log->ip_address)
                                        <span style="font-size:.6875rem;color:var(--gray-300);font-family:'SF Mono','Fira Code',monospace;">{{ $log->ip_address }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:3rem;">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3>No activity recorded yet</h3>
                        <p>Actions will appear here as they happen.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($logs->hasPages())
    <div style="margin-top:1rem;">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
