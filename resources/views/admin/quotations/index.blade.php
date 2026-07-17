@extends('layouts.admin')
@section('title', 'All Quotations')
@section('content')
<div class="fade-in">
    <x-page-header title="Quotation Oversight" subtitle="View all quotations across every company" />

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <form method="GET" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <x-form-input label="Search" name="search" value="{{ request('search') }}" placeholder="Quote # or client name..." />
                </div>
                <div style="min-width:140px;">
                    <x-form-select name="status" label="Status" value="{{ request('status') }}" placeholder="All"
                        :options="['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined']" />
                </div>
                <div style="min-width:160px;">
                    <x-form-select name="company_id" label="Company" value="{{ request('company_id') }}" placeholder="All"
                        :options="$companies->pluck('name', 'id')->toArray()" />
                </div>
                <div style="min-width:150px;">
                    <x-form-input label="From" name="from_date" type="date" value="{{ request('from_date') }}" />
                </div>
                <div style="min-width:150px;">
                    <x-form-input label="To" name="to_date" type="date" value="{{ request('to_date') }}" />
                </div>
                <button class="btn btn-brand">Filter</button>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Company</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($quotations as $q)
                <tr>
                    <td style="font-weight:600;color:var(--brand-600);">{{ $q->quote_number }}</td>
                    <td style="color:var(--surface-600);">{{ $q->user->company?->name ?? 'N/A' }}</td>
                    <td style="color:var(--surface-600);">{{ $q->client->name }}</td>
                    <td style="color:var(--surface-600);">{{ $q->issue_date->format('M d, Y') }}</td>
                    <td style="font-weight:600;">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                    <td>
                        <x-quotation-status-badge :status="$q->status" />
                    </td>
                    <td><a href="/admin/quotations/{{ $q->id }}" class="btn btn-ghost btn-icon" title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a></td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <x-empty-state title="No quotations found" description="Try adjusting your filters or check back later." icon="quote" />
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $quotations->links() }}</div>
</div>
@endsection
