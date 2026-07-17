@extends('layouts.admin')
@section('title', 'Company Users')
@section('content')
<div class="fade-in">
    <x-page-header title="Company Users" subtitle="Manage company admin and staff accounts">
        <x:slot name="actions">
            <a href="/admin/company-users/create" class="btn btn-brand">+ New Company User</a>
        </x:slot>
    </x-page-header>

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <form method="GET" action="/admin/company-users" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <x-form-input label="Search" name="search" value="{{ request('search') }}" placeholder="Name or email..." />
                </div>
                <div style="min-width:180px;">
                    <x-form-select name="company_id" label="Company" value="{{ request('company_id') }}" placeholder="All Companies"
                        :options="$companies->pluck('name', 'id')->toArray()" />
                </div>
                <div style="min-width:150px;">
                    <x-form-select name="role" label="Role" value="{{ request('role') }}" placeholder="All Roles"
                        :options="['company_admin' => 'Company Admin', 'staff' => 'Staff']" />
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-brand">Filter</button>
                    <a href="/admin/company-users" class="btn btn-ghost" style="border:1px solid var(--surface-200);">Clear</a>
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
                    <th>Company</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td style="font-weight:600;">{{ $u->name }}</td>
                    <td style="color:var(--surface-600);">{{ $u->email }}</td>
                    <td>
                        @if($u->company)
                            <span class="badge" style="background:var(--info-100);color:var(--info-700);">{{ $u->company->name }}</span>
                        @else
                            <span class="badge badge-inactive">—</span>
                        @endif
                    </td>
                    <td>
                        @if($u->role === 'company_admin')
                            <span class="badge badge-sent">Company Admin</span>
                        @else
                            <span class="badge badge-active">Staff</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.25rem;">
                            <a href="/admin/company-users/{{ $u->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="/admin/company-users/{{ $u->id }}" onsubmit="return confirm('Delete this company user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <x-empty-state title="No company users found" description="Create a new company user to get started." icon="client" action="/admin/company-users/create" actionLabel="+ New Company User" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $users->links() }}
    </div>
</div>
@endsection
