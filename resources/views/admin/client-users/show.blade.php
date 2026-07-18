@extends('layouts.admin')
@section('title', $clientUser->name)
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <a href="/admin/client-users" class="btn btn-ghost btn-sm" style="border:1px solid var(--gray-200);">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <div class="avatar avatar-lg" style="background:var(--purple-50);color:var(--purple-600);font-size:.875rem;">{{ strtoupper(substr($clientUser->name, 0, 2)) }}</div>
            <div>
                <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">{{ $clientUser->name }}</h1>
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.125rem;">
                    <span style="font-size:.8125rem;color:var(--gray-400);">{{ $clientUser->email }}</span>
                    @if($clientUser->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.375rem;">
            <form method="POST" action="/admin/client-users/{{ $clientUser->id }}/status" style="display:inline;">
                @csrf @method('PATCH')
                @if($clientUser->is_active)
                    <button class="btn btn-sm" style="background:var(--amber-50);color:var(--amber-700);border:1px solid var(--amber-100);">Deactivate</button>
                @else
                    <button class="btn btn-sm" style="background:var(--emerald-50);color:var(--emerald-700);border:1px solid var(--emerald-100);">Activate</button>
                @endif
            </form>
        </div>
    </div>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon brand">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Companies</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $clientUser->companies->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Clients</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $clientUser->clients->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-label">Last Login</div>
                <div class="stat-value" style="font-size:1.125rem;">{{ $clientUser->last_login_at ? $clientUser->last_login_at->diffForHumans() : 'Never' }}</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        <x-card>
            <x-slot name="title">Contact Information</x-slot>
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Name</div>
                    <div style="font-size:.875rem;color:var(--gray-800);font-weight:500;">{{ $clientUser->name }}</div>
                </div>
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Email</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $clientUser->email }}</div>
                </div>
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Phone</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $clientUser->phone ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.125rem;">Created</div>
                    <div style="font-size:.875rem;color:var(--gray-800);">{{ $clientUser->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </x-card>

        <x-card>
            <x-slot name="title">Companies ({{ $clientUser->companies->count() }})</x-slot>
            @forelse($clientUser->companies as $company)
                <div style="display:flex;align-items:center;gap:.5rem;padding:.5rem 0;@if(!$loop->last)border-bottom:1px solid var(--gray-100);@endif">
                    <div class="avatar avatar-sm" style="background:var(--blue-50);color:var(--blue-600);font-size:.5rem;">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
                    <span style="font-weight:500;color:var(--gray-800);font-size:.8125rem;">{{ $company->name }}</span>
                </div>
            @empty
                <p style="color:var(--gray-400);font-size:.8125rem;padding:.5rem 0;">No companies assigned.</p>
            @endforelse
        </x-card>
    </div>

    <x-card>
        <x-slot name="title">Clients ({{ $clientUser->clients->count() }})</x-slot>
        <div class="d-card-body-compact">
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
                        <td>
                            <span class="cell-main">{{ $client->name }}</span>
                        </td>
                        <td>
                            <span style="color:var(--gray-500);">{{ $client->email ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="color:var(--gray-500);">{{ $client->phone ?? '—' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:2rem;">
                                <p style="color:var(--gray-400);">No clients associated with this user.</p>
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