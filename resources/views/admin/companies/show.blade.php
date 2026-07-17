@extends('layouts.admin')
@section('title', $company->name)
@section('content')
<div class="fade-in">
    <x-page-header title="{{ $company->name }}" subtitle="{{ $company->email }}">
        <x-slot name="actions">
            <a href="/admin/companies/{{ $company->id }}/edit" class="btn btn-brand btn-icon" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            <a href="/admin/companies" class="btn btn-ghost btn-sm" style="border:1px solid var(--surface-200);">Back</a>
        </x-slot>
    </x-page-header>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.25rem;">
        <div class="stat-card">
            <div class="stat-icon info">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>
            </div>
            <div>
                <div class="stat-label">Status</div>
                <div style="margin-top:.25rem;">
                    @if($company->status === 'active')<span class="badge badge-active">Active</span>
                    @elseif($company->status === 'blocked')<span class="badge badge-blocked">Blocked</span>
                    @else<span class="badge badge-inactive">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <div class="stat-label">Active Package</div>
                @php $activePkg = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first(); @endphp
                <div class="stat-value" style="font-size:1rem;">{{ $activePkg?->package?->name ?? 'No package' }}</div>
                @if($activePkg)
                    <div class="stat-sub">Expires: {{ $activePkg->end_date->format('M d, Y') }}</div>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value" style="font-size:1rem;">{{ $company->users_count ?? $company->users->count() }}</div>
            </div>
        </div>
    </div>

    <x-card class="mb-4">
        <x-slot name="title">Assign Package</x-slot>
        <form method="POST" action="/admin/companies/{{ $company->id }}/assign-package" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div style="flex:1;min-width:200px;">
                <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Package</label>
                <select name="package_id" required style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                    @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}">{{ $pkg->name }} - ${{ $pkg->price }}/{{ $pkg->duration_days }}d</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:160px;">
                <label style="display:block;font-size:.8125rem;font-weight:600;color:var(--surface-700);margin-bottom:.375rem;">Start Date</label>
                <input type="date" name="start_date" value="{{ now()->toDateString() }}" required
                    style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
            </div>
            <button class="btn btn-brand">Assign</button>
        </form>
    </x-card>

    <x-card>
        <x-slot name="title">Users</x-slot>
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
            @foreach($company->users as $user)
                <tr>
                    <td style="font-weight:600;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge badge-draft">{{ $user->role }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-card>
</div>
@endsection