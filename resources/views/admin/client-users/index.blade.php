@extends('layouts.admin')
@section('title', 'Client Portal Users')
@section('content')
<div class="fade-in">
    <x-page-header title="Client Portal Users" subtitle="Manage client portal accounts" />

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <form method="GET" action="/admin/client-users" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <x-form-input label="Search" name="search" value="{{ request('search') }}" placeholder="Name or email..." />
                </div>
                <div style="min-width:180px;">
                    <x-form-select name="company_id" label="Company" value="{{ request('company_id') }}" placeholder="All Companies"
                        :options="$companies->pluck('name', 'id')->toArray()" />
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-brand">Filter</button>
                    <a href="/admin/client-users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Companies</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clientUsers as $user)
                <tr>
                    <td style="font-weight:600;"><a href="/admin/client-users/{{ $user->id }}" style="color:var(--brand-600);text-decoration:none;">{{ $user->name }}</a></td>
                    <td style="color:var(--surface-600);">{{ $user->email }}</td>
                    <td style="color:var(--surface-600);">{{ $user->phone ?: '—' }}</td>
                    <td>
                        @forelse($user->companies as $company)
                            <span class="badge" style="background:var(--info-100);color:var(--info-700);">{{ $company->name }}</span>
                        @empty
                            <span class="badge badge-inactive">—</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->is_active)
                            <x-status-badge status="active">Active</x-status-badge>
                        @else
                            <x-status-badge status="inactive">Inactive</x-status-badge>
                        @endif
                    </td>
                    <td style="font-size:.75rem;color:var(--surface-400);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/admin/client-users/{{ $user->id }}" class="btn btn-ghost btn-icon" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <form method="POST" action="/admin/client-users/{{ $user->id }}/status" style="display:inline;">
                                @csrf @method('PATCH')
                                @if($user->is_active)
                                    <button class="btn btn-ghost btn-icon" title="Deactivate" style="color:var(--warning-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 11-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                    </button>
                                @else
                                    <button class="btn btn-ghost btn-icon" title="Activate" style="color:var(--success-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </button>
                                @endif
                            </form>
                            <form method="POST" action="/admin/client-users/{{ $user->id }}" onsubmit="return confirm('Delete this client user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-icon" title="Delete" style="color:var(--danger-600);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <x-empty-state title="No client users found" description="No client portal accounts match your search criteria." icon="client" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $clientUsers->links() }}
    </div>
</div>
@endsection
