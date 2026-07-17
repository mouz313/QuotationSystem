@extends('layouts.admin')
@section('title', $clientUser->name)
@section('content')
<div class="fade-in">
    <x-page-header title="{{ $clientUser->name }}" subtitle="{{ $clientUser->email }}">
        <x-slot name="actions">
            <form method="POST" action="/admin/client-users/{{ $clientUser->id }}/status" style="display:inline;">
                @csrf @method('PATCH')
                @if($clientUser->is_active)
                    <button class="btn btn-ghost btn-sm" style="border:1px solid var(--warning-200);color:var(--warning-700);">Deactivate</button>
                @else
                    <button class="btn btn-ghost btn-sm" style="border:1px solid var(--success-200);color:var(--success-700);">Activate</button>
                @endif
            </form>
            <a href="/admin/client-users" class="btn btn-ghost btn-sm" style="border:1px solid var(--surface-200);">Back</a>
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
                    @if($clientUser->is_active)
                        <x-status-badge status="active">Active</x-status-badge>
                    @else
                        <x-status-badge status="inactive">Inactive</x-status-badge>
                    @endif
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Companies</div>
                <div class="stat-value" style="font-size:1rem;">{{ $clientUser->companies->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Clients</div>
                <div class="stat-value" style="font-size:1rem;">{{ $clientUser->clients->count() }}</div>
            </div>
        </div>
    </div>

    <div class="stat-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:1.25rem;">
        <x-card>
            <x-slot name="title">Contact Information</x-slot>
            <table class="d-table">
                <tbody>
                    <tr><td style="font-weight:600;width:140px;">Name</td><td>{{ $clientUser->name }}</td></tr>
                    <tr><td style="font-weight:600;">Email</td><td>{{ $clientUser->email }}</td></tr>
                    <tr><td style="font-weight:600;">Phone</td><td>{{ $clientUser->phone ?: '—' }}</td></tr>
                    <tr><td style="font-weight:600;">Last Login</td><td>{{ $clientUser->last_login_at ? $clientUser->last_login_at->format('M d, Y h:i A') : 'Never' }}</td></tr>
                    <tr><td style="font-weight:600;">Created</td><td>{{ $clientUser->created_at->format('M d, Y') }}</td></tr>
                </tbody>
            </table>
        </x-card>
        <x-card>
            <x-slot name="title">Companies</x-slot>
            @forelse($clientUser->companies as $company)
                <div style="display:flex;align-items:center;gap:.5rem;padding:.5rem 0;@if(!$loop->last)border-bottom:1px solid var(--surface-100);@endif">
                    <span class="badge" style="background:var(--info-100);color:var(--info-700);">{{ $company->name }}</span>
                </div>
            @empty
                <p style="color:var(--surface-400);font-size:.875rem;">No companies assigned.</p>
            @endforelse
        </x-card>
    </div>

    <x-card>
        <x-slot name="title">Clients ({{ $clientUser->clients->count() }})</x-slot>
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clientUser->clients as $client)
                <tr>
                    <td style="font-weight:600;">{{ $client->name }}</td>
                    <td style="color:var(--surface-600);">{{ $client->email ?? '—' }}</td>
                    <td style="color:var(--surface-600);">{{ $client->phone ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">
                    <x-empty-state title="No clients" description="This user has no associated clients." icon="client" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </x-card>
</div>
@endsection
