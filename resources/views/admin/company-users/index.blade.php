@extends('layouts.admin')
@section('title', 'Company Users')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Company Users</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage company admin and staff accounts</p>
        </div>
        <a href="/admin/company-users/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Company User
        </a>
    </div>

    <div class="d-card" style="margin-bottom:1rem;">
        <div class="d-card-body" style="padding:.75rem 1.25rem;">
            <form method="GET" action="/admin/company-users" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
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
                <div class="filter-pills">
                    <a href="?role=" class="filter-pill {{ !request('role') ? 'active' : '' }}">All Roles</a>
                    <a href="?role=company_admin" class="filter-pill {{ request('role') === 'company_admin' ? 'active' : '' }}">Admin</a>
                    <a href="?role=staff" class="filter-pill {{ request('role') === 'staff' ? 'active' : '' }}">Staff</a>
                </div>
                <button class="btn btn-brand btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Company</th>
                    <th>Role</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div class="avatar avatar-sm" style="background:var(--emerald-50);color:var(--emerald-600);font-size:.55rem;">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                            <div>
                                <div class="cell-main">{{ $u->name }}</div>
                                <div class="cell-sub">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($u->company)
                            <span class="badge" style="background:var(--blue-50);color:var(--blue-600);">{{ $u->company->name }}</span>
                        @else
                            <span style="color:var(--gray-400);font-size:.8125rem;">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($u->role === 'company_admin')
                            <span class="badge badge-sent">Company Admin</span>
                        @else
                            <span class="badge badge-active">Staff</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/company-users/{{ $u->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="/admin/company-users/{{ $u->id }}" onsubmit="return confirm('Delete this company user?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3>No company users found</h3>
                            <p>Create a new company user to get started.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="margin-top:1rem;">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
