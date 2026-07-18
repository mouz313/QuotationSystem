@extends('layouts.admin')
@section('title', 'All Quotations')
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div>
            <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;">Quotations</h1>
            <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">View all quotations across every company</p>
        </div>
    </div>

    <div class="d-card" style="margin-bottom:1rem;">
        <div class="d-card-body" style="padding:.75rem 1.25rem;">
            <form method="GET" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                <div class="search-input" style="flex:1;min-width:200px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote # or client name...">
                </div>
                <div class="filter-pills">
                    <a href="?status=" class="filter-pill {{ !request('status') ? 'active' : '' }}">All</a>
                    <a href="?status=draft" class="filter-pill {{ request('status') === 'draft' ? 'active' : '' }}">Draft</a>
                    <a href="?status=sent" class="filter-pill {{ request('status') === 'sent' ? 'active' : '' }}">Sent</a>
                    <a href="?status=accepted" class="filter-pill {{ request('status') === 'accepted' ? 'active' : '' }}">Accepted</a>
                    <a href="?status=declined" class="filter-pill {{ request('status') === 'declined' ? 'active' : '' }}">Declined</a>
                </div>
                <div style="display:flex;gap:.375rem;align-items:center;">
                    <select name="company_id" class="form-select" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-input" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;" placeholder="From">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-input" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;" placeholder="To">
                    <button class="btn btn-brand btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-card" style="overflow:hidden;">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Client</th>
                    <th>Company</th>
                    <th>Date</th>
                    <th style="text-align:right;">Total</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($quotations as $q)
                <tr>
                    <td>
                        <a href="/admin/quotations/{{ $q->id }}" style="text-decoration:none;">
                            <span style="font-weight:700;color:var(--brand-600);font-family:'SF Mono','Fira Code',monospace;font-size:.8125rem;">{{ $q->quote_number }}</span>
                        </a>
                    </td>
                    <td>
                        <div class="cell-main">{{ $q->client->name }}</div>
                        <div class="cell-sub">{{ $q->client->email }}</div>
                    </td>
                    <td>
                        <span style="color:var(--gray-600);">{{ $q->user->company?->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span style="font-size:.75rem;color:var(--gray-400);">{{ $q->issue_date->format('M d, Y') }}</span>
                    </td>
                    <td style="text-align:right;">
                        <span style="font-weight:700;color:var(--gray-900);font-variant-numeric:tabular-nums;">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</span>
                    </td>
                    <td>
                        <x-quotation-status-badge :status="$q->status" />
                    </td>
                    <td style="text-align:right;">
                        <a href="/admin/quotations/{{ $q->id }}" class="btn btn-ghost btn-icon" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3>No quotations found</h3>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($quotations->hasPages())
    <div style="margin-top:1rem;">
        {{ $quotations->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
