@extends('layouts.admin')
@section('title', 'Companies')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Companies</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage all tenant companies</p>
        </div>
        <a href="/admin/companies/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Company
        </a>
    </div>

    <div class="d-card" style="margin-bottom:1rem;">
        <div class="d-card-body" style="padding:.75rem 1.25rem;">
            <form method="GET" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                <div class="search-input" style="flex:1;min-width:200px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies...">
                </div>
                <div class="filter-pills">
                    <a href="?status=" class="filter-pill {{ !request('status') ? 'active' : '' }}">All</a>
                    <a href="?status=active" class="filter-pill {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
                    <a href="?status=inactive" class="filter-pill {{ request('status') === 'inactive' ? 'active' : '' }}">Inactive</a>
                    <a href="?status=blocked" class="filter-pill {{ request('status') === 'blocked' ? 'active' : '' }}">Blocked</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Active Package</th>
                    <th>Users</th>
                    <th>Created</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($companies as $company)
                @php $activePkg = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first(); @endphp
                <tr>
                    <td>
                        <a href="/admin/companies/{{ $company->id }}" style="text-decoration:none;">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div class="avatar avatar-brand" style="font-size:.65rem;">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
                                <div>
                                    <div class="cell-main">{{ $company->name }}</div>
                                    <div class="cell-sub">{{ $company->email }}</div>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-{{ $company->status }}">{{ ucfirst($company->status) }}</span>
                    </td>
                    <td>
                        @if($activePkg)
                            <div style="display:flex;align-items:center;gap:.375rem;">
                                <span style="font-weight:600;color:var(--gray-800);">{{ $activePkg->package->name }}</span>
                                <span class="badge badge-active" style="font-size:.6rem;">Active</span>
                            </div>
                            <div class="cell-sub">Expires {{ $activePkg->end_date->format('M d, Y') }}</div>
                        @else
                            <span style="color:var(--gray-400);font-size:.8125rem;">No package</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:600;color:var(--gray-800);">{{ $company->users_count ?? $company->users->count() }}</span>
                    </td>
                    <td>
                        <span style="font-size:.75rem;color:var(--gray-400);">{{ $company->created_at->diffForHumans() }}</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:.25rem;justify-content:flex-end;">
                            <a href="/admin/companies/{{ $company->id }}" class="btn btn-ghost btn-icon" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="/admin/companies/{{ $company->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($company->status !== 'blocked')
                            <form method="POST" action="/admin/companies/{{ $company->id }}/status" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="blocked">
                                <button class="btn btn-ghost btn-icon" title="Block" style="color:var(--red-500);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <h3>No companies found</h3>
                            <p>No companies match your current filters.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
    <div style="margin-top:1rem;">
        {{ $companies->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection