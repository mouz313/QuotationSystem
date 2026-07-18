@extends('layouts.admin')
@section('title', $company->name)
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <a href="/admin/companies" class="btn btn-ghost btn-sm" style="border:1px solid var(--gray-200);">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <div class="avatar avatar-lg avatar-brand" style="font-size:.875rem;">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
            <div>
                <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">{{ $company->name }}</h1>
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.125rem;">
                    <span style="font-size:.8125rem;color:var(--gray-400);">{{ $company->email }}</span>
                    <span class="badge badge-{{ $company->status }}">{{ ucfirst($company->status) }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="/admin/companies/{{ $company->id }}/edit" class="btn btn-outline btn-sm">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form method="POST" action="/admin/companies/{{ $company->id }}/status" style="display:inline;">
                @csrf @method('PATCH')
                @if($company->status === 'active')
                    <input type="hidden" name="status" value="inactive">
                    <button class="btn btn-sm" style="background:var(--amber-50);color:var(--amber-700);border:1px solid var(--amber-100);">Deactivate</button>
                @else
                    <input type="hidden" name="status" value="active">
                    <button class="btn btn-sm" style="background:var(--emerald-50);color:var(--emerald-700);border:1px solid var(--emerald-100);">Activate</button>
                @endif
            </form>
        </div>
    </div>

    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1.5rem;">
        @php $activePkg = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first(); @endphp
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <div class="stat-label">Active Package</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $activePkg?->package?->name ?? 'None' }}</div>
                @if($activePkg)
                    <div class="stat-sub">Expires {{ $activePkg->end_date->format('M d, Y') }}</div>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $company->users_count ?? $company->users->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Quotations</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $company->quotations_count ?? $company->quotations->count() ?? '—' }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Created</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $company->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        <x-card>
            <x-slot name="title">Assign Package</x-slot>
            <form method="POST" action="/admin/companies/{{ $company->id }}/assign-package" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Package</label>
                    <select name="package_id" required class="form-select">
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}">{{ $pkg->name }} - ${{ $pkg->price }}/{{ $pkg->duration_days }}d</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ now()->toDateString() }}" required class="form-input">
                </div>
                <button class="btn btn-brand" style="align-self:flex-start;">Assign Package</button>
            </form>
        </x-card>

        <x-card>
            <x-slot name="title">Contact Info</x-slot>
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Email</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $company->email }}</div>
                </div>
                @if($company->phone ?? null)
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Phone</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $company->phone }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Registered</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $company->created_at->format('M d, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <x-slot name="title">Users ({{ $company->users->count() }})</x-slot>
        <div class="d-card-body-compact">
            <table class="d-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($company->users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <div class="avatar avatar-sm" style="background:var(--gray-100);color:var(--gray-600);font-size:.55rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div>
                                    <div class="cell-main">{{ $user->name }}</div>
                                    <div class="cell-sub">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-sent">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </td>
                        <td>
                            <span style="font-size:.75rem;color:var(--gray-400);">{{ $user->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:2rem;">
                                <p style="color:var(--gray-400);">No users in this company yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection