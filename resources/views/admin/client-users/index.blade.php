@extends('layouts.admin')
@section('title', 'Client Portal Users')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Client Portal Users</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage client portal accounts</p>
        </div>
    </div>

    <div class="d-card" style="margin-bottom:1rem;">
        <div class="d-card-body" style="padding:.75rem 1.25rem;">
            <form method="GET" action="/admin/client-users" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                <div class="search-input" style="flex:1;min-width:200px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email...">
                </div>
                <select name="company_id" class="form-select" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-brand btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Companies</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clientUsers as $user)
                <tr>
                    <td>
                        <a href="/admin/client-users/{{ $user->id }}" style="text-decoration:none;">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div class="avatar avatar-sm" style="background:var(--purple-50);color:var(--purple-600);font-size:.55rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div>
                                    <div class="cell-main" style="color:var(--brand-600);">{{ $user->name }}</div>
                                    <div class="cell-sub">{{ $user->email }}</div>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td>
                        <span style="color:var(--gray-500);font-size:.8125rem;">{{ $user->phone ?: '—' }}</span>
                    </td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                            @forelse($user->companies as $company)
                                <span class="badge" style="background:var(--blue-50);color:var(--blue-600);">{{ $company->name }}</span>
                            @empty
                                <span style="color:var(--gray-400);font-size:.8125rem;">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.75rem;color:var(--gray-400);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/client-users/{{ $user->id }}" class="btn btn-ghost btn-icon" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <form method="POST" action="/admin/client-users/{{ $user->id }}/status" style="display:inline;">
                                @csrf @method('PATCH')
                                @if($user->is_active)
                                    <button class="btn btn-ghost btn-icon" title="Deactivate" style="color:var(--amber-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 11-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                    </button>
                                @else
                                    <button class="btn btn-ghost btn-icon" title="Activate" style="color:var(--emerald-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </button>
                                @endif
                            </form>
                            <form method="POST" action="/admin/client-users/{{ $user->id }}" onsubmit="return confirm('Delete this client user?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <h3>No client users found</h3>
                            <p>No client portal accounts match your search criteria.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($clientUsers->hasPages())
    <div style="margin-top:1rem;">
        {{ $clientUsers->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
