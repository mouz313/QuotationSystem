@extends('layouts.admin')
@section('title', 'Packages')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Packages</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Manage subscription plans for companies</p>
        </div>
        <a href="/admin/packages/create" class="btn btn-brand">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Package
        </a>
    </div>

    <div class="pricing-grid">
        @forelse($packages as $pkg)
            <div class="pricing-card {{ $pkg->company_packages_count > 0 ? 'popular' : '' }}">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
                    <div>
                        <h3 style="font-size:1.0625rem;font-weight:700;color:var(--gray-900);">{{ $pkg->name }}</h3>
                        <div style="font-size:.75rem;color:var(--gray-400);margin-top:.125rem;">
                            {{ $pkg->company_packages_count }} {{ Str::plural('subscription', $pkg->company_packages_count) }}
                        </div>
                    </div>
                    @if($pkg->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </div>

                <div style="margin-bottom:1rem;">
                    <span class="price">
                        @if($pkg->price == 0)
                            Free
                        @else
                            ${{ number_format($pkg->price, 2) }}
                        @endif
                    </span>
                    <span class="price-period">/ {{ $pkg->duration_days }} days</span>
                </div>

                @if($pkg->description)
                    <p style="font-size:.8125rem;color:var(--gray-500);margin-bottom:1rem;">{{ $pkg->description }}</p>
                @endif

                <ul class="feature-list">
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $pkg->max_users }} {{ Str::plural('user', $pkg->max_users) }}
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $pkg->max_clients }} {{ Str::plural('client', $pkg->max_clients) }}
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $pkg->max_quotations }} {{ Str::plural('quotation', $pkg->max_quotations) }}
                    </li>
                </ul>

                <div style="display:flex;gap:.375rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--gray-100);">
                    <a href="/admin/packages/{{ $pkg->id }}/edit" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    @if($pkg->price != 0)
                    <form method="POST" action="/admin/packages/{{ $pkg->id }}" onsubmit="return confirm('Delete this package?')" style="flex:1;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm" style="width:100%;justify-content:center;background:var(--red-50);color:var(--red-600);border:1px solid var(--red-100);">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
        @endforelse

        <a href="/admin/packages/create" class="pricing-card-ghost">
            <svg style="width:2.5rem;height:2.5rem;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span style="font-size:.875rem;font-weight:600;margin-top:.5rem;">Create Package</span>
            <span style="font-size:.75rem;margin-top:.125rem;">Add a new subscription plan</span>
        </a>
    </div>
</div>
@endsection
