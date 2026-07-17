@extends('layouts.admin')
@section('title', 'Packages')
@section('content')
<div class="fade-in">
    <x-page-header title="Packages" subtitle="Manage subscription plans">
        <x-slot name="actions">
            <a href="/admin/packages/create" class="btn btn-brand">+ New Package</a>
        </x-slot>
    </x-page-header>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);">
        @forelse($packages as $pkg)
            <div class="d-card" style="padding:1.25rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.75rem;">
                    <h3 style="font-size:1rem;font-weight:700;color:var(--surface-900);">{{ $pkg->name }}</h3>
                    @if($pkg->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </div>
                <div style="font-size:1.75rem;font-weight:800;color:var(--brand-600);margin-bottom:.25rem;">
                    @if($pkg->price == 0)
                        Free
                    @else
                        ${{ number_format($pkg->price, 2) }}
                    @endif
                    <span style="font-size:.8125rem;font-weight:500;color:var(--surface-500);">/{{ $pkg->duration_days }}d</span>
                </div>
                <p style="font-size:.8125rem;color:var(--surface-500);margin-bottom:1rem;">{{ $pkg->description }}</p>
                <div style="font-size:.8125rem;color:var(--surface-600);margin-bottom:1rem;">
                    <div>Users: <strong>{{ $pkg->max_users }}</strong></div>
                    <div>Clients: <strong>{{ $pkg->max_clients }}</strong></div>
                    <div>Quotations: <strong>{{ $pkg->max_quotations }}</strong></div>
                </div>
                <div style="font-size:.7rem;color:var(--surface-400);margin-bottom:.75rem;">{{ $pkg->company_packages_count }} active subscriptions</div>
                <div style="display:flex;gap:.25rem;">
                    <a href="/admin/packages/{{ $pkg->id }}/edit" class="btn btn-ghost btn-icon" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="/admin/packages/{{ $pkg->id }}" onsubmit="return confirm('Delete this package?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;">
                <x-card>
                    <x-empty-state icon="info" title="No packages created yet" description="Create your first subscription package to get started." action="/admin/packages/create" actionLabel="Create Package" />
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
