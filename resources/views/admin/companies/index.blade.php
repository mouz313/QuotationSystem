@extends('layouts.admin')
@section('title', 'Companies')
@section('content')
<div class="fade-in">
    <x-page-header title="Companies" subtitle="Manage all tenant companies">
        <x-slot name="actions">
            <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                    style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
                <select name="status" style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline-none;background:var(--surface-0);">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
                <button class="btn btn-brand btn-sm">Filter</button>
            </form>
        </x-slot>
    </x-page-header>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Package</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($companies as $company)
                <tr>
                    <td style="font-weight:600;"><a href="/admin/companies/{{ $company->id }}" style="color:var(--brand-600);text-decoration:none;">{{ $company->name }}</a></td>
                    <td>{{ $company->email }}</td>
                    <td>
                        @if($company->status === 'active')<span class="badge badge-active">Active</span>
                        @elseif($company->status === 'blocked')<span class="badge badge-blocked">Blocked</span>
                        @else<span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first()?->package?->name ?? 'None' }}</td>
                    <td style="font-size:.75rem;color:var(--surface-400);">{{ $company->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:.375rem;">
                            <form method="POST" action="/admin/companies/{{ $company->id }}/status">
                                @csrf @method('PATCH')
                                @if($company->status === 'active')
                                    <input type="hidden" name="status" value="inactive">
                                    <button class="btn btn-sm" style="background:var(--warning-50);color:var(--warning-700);border:1px solid var(--warning-100);">Deactivate</button>
                                @else
                                    <input type="hidden" name="status" value="active">
                                    <button class="btn btn-sm" style="background:var(--success-50);color:var(--success-700);border:1px solid var(--success-100);">Activate</button>
                                @endif
                            </form>
                            @if($company->status !== 'blocked')
                            <form method="POST" action="/admin/companies/{{ $company->id }}/status">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="blocked">
                                <button class="btn btn-sm" style="background:var(--danger-50);color:var(--danger-700);border:1px solid var(--danger-100);">Block</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state icon="client" title="No companies found" description="No companies match your search criteria." />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $companies->withQueryString()->links() }}</div>
</div>
@endsection