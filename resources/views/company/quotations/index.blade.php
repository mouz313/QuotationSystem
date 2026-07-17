@extends('layouts.app')
@section('title', 'Quotations')
@section('content')

<x-page-header title="Quotations" subtitle="Manage all your quotations">
    <x-slot name="actions">
        <a href="/quotations/export" class="btn btn-ghost" style="border:1px solid var(--surface-200);font-size:.8125rem;">Export CSV</a>
        <a href="/quotations/create" class="btn btn-brand" style="font-size:.8125rem;">+ New Quotation</a>
    </x-slot>
</x-page-header>

<x-card class="fade-in" style="margin-bottom:1rem;">
    <div style="padding:1rem 1.25rem;">
        <form method="GET" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:10rem;">
                <label style="display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.25rem;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote # or client name..." style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;focus:border-color:var(--brand-500);">
            </div>
            <div style="min-width:8rem;">
                <label style="display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.25rem;">Status</label>
                <select name="status" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;background:var(--surface-0);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right .5rem center;background-size:1.25rem;padding-right:2.25rem;">
                    <option value="">All</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="opened" {{ request('status') === 'opened' ? 'selected' : '' }}>Opened</option>
                    <option value="change_requested" {{ request('status') === 'change_requested' ? 'selected' : '' }}>Change Requested</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div style="min-width:8rem;">
                <label style="display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.25rem;">From</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
            </div>
            <div style="min-width:8rem;">
                <label style="display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.25rem;">To</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" style="width:100%;padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);outline:none;">
            </div>
            <button type="submit" class="btn btn-brand" style="white-space:nowrap;">Filter</button>
            <a href="/quotations" class="btn btn-ghost" style="border:1px solid var(--surface-200);white-space:nowrap;">Clear</a>
        </form>
    </div>
</x-card>

<div class="d-card fade-in" style="overflow:hidden;">
    <form id="bulkForm" method="POST" action="/quotations/bulk-delete" onsubmit="return confirm('Delete selected quotations?')">
        @csrf
        <div style="overflow-x:auto;">
            <table class="d-table">
                <thead>
                    <tr>
                        <th style="width:2.5rem;"><input type="checkbox" id="selectAll" onchange="toggleAll()" style="accent-color:var(--brand-600);"></th>
                        <th>Quote #</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Expiry</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $q->id }}" class="row-checkbox" style="accent-color:var(--brand-600);"></td>
                        <td style="font-weight:600;"><a href="/quotations/{{ $q->id }}" style="color:var(--brand-600);text-decoration:none;">{{ $q->quote_number }}</a></td>
                        <td>{{ $q->client->name }}</td>
                        <td>{{ $q->issue_date->format('M d, Y') }}</td>
                        <td>{{ $q->expiry_date?->format('M d, Y') ?? '-' }}</td>
                        <td style="font-weight:600;">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.375rem;">
                                <x-status-badge :status="$q->status">{{ ucfirst($q->status) }}</x-status-badge>
                                @if($q->type === 'milestone')
                                    <span style="padding:.1rem .375rem;font-size:.6rem;font-weight:700;border-radius:999px;background:oklch(0.95 0.04 300);color:oklch(0.50 0.16 300);text-transform:uppercase;letter-spacing:.04em;">M</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:.25rem;align-items:center;">
                                <a href="/quotations/{{ $q->id }}" class="btn btn-ghost btn-icon" title="View" style="color:var(--brand-600);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="/quotations/{{ $q->id }}/pdf" class="btn btn-ghost btn-icon" title="PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                @if($q->status === 'draft')
                                    <form method="POST" action="/quotations/{{ $q->id }}/send-email" style="display:inline;" onsubmit="return confirm('Send this quotation to {{ $q->client->email }}?')">
                                        @csrf
                                        <button class="btn btn-icon" title="Send" style="color:var(--success-600);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if($q->status === 'change_requested')
                                    <a href="/quotations/{{ $q->id }}/edit" class="btn btn-ghost btn-icon" title="Amend" style="color:oklch(0.50 0.16 300);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state icon="quote" title="{{ request('search') || request('status') ? 'No quotations match your filters.' : 'No quotations yet.' }}" description="{{ request('search') || request('status') ? 'Try adjusting your search or filters.' : 'Create your first quotation to get started.' }}" action="{{ !request('search') && !request('status') ? '/quotations/create' : '' }}" actionLabel="{{ !request('search') && !request('status') ? '+ New Quotation' : '' }}" />
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($quotations->count() > 0)
        <div style="padding:.75rem 1rem;border-top:1px solid var(--surface-100);background:var(--surface-50);display:flex;align-items:center;gap:.5rem;">
            <button class="btn btn-icon" title="Delete Selected" style="color:var(--danger-600);">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        @endif
    </form>
</div>

<div style="margin-top:1rem;">{{ $quotations->links() }}</div>

<script>
function toggleAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = document.getElementById('selectAll').checked);
}
</script>
@endsection
