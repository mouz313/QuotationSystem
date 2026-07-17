@extends('client.layouts.client')
@section('title', 'My Quotations')
@section('header-title', 'Client Portal')
@section('header-sub', 'My Quotations')
@section('content')

<x-page-header title="My Quotations" subtitle="{{ $quotations->total() }} quotation{{ $quotations->total() !== 1 ? 's' : '' }} found" />

<div class="d-card fade-in" style="margin-bottom:1rem;">
    <form method="GET" action="/client/quotations" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);width:1rem;height:1rem;color:var(--surface-400);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by quote number..." style="width:100%;padding:.5rem .75rem .5rem 2.25rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;transition:border-color .15s;" onfocus="this.style.borderColor='var(--brand-400)'" onblur="this.style.borderColor='var(--surface-200)'">
        </div>
        <select name="status" onchange="this.form.submit()" style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;min-width:140px;">
            <option value="">All Statuses</option>
            <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="opened" {{ $status === 'opened' ? 'selected' : '' }}>Opened</option>
            <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="declined" {{ $status === 'declined' ? 'selected' : '' }}>Declined</option>
            <option value="change_requested" {{ $status === 'change_requested' ? 'selected' : '' }}>Changes Requested</option>
        </select>
        <select name="company" onchange="this.form.submit()" style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;min-width:160px;">
            <option value="">All Companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ $companyId == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
        @if($search || $status || $companyId)
            <a href="/client/quotations" style="padding:.5rem .75rem;font-size:.8125rem;font-weight:600;color:var(--danger-600);border:1px solid var(--danger-200);border-radius:.5rem;text-decoration:none;transition:background .15s;white-space:nowrap;" onmouseover="this.style.background='var(--danger-50)'" onmouseout="this.style.background='transparent'">Clear</a>
        @endif
    </form>
</div>

<div class="d-card fade-in fade-in-1">
    <div class="overflow-x-auto">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Company</th>
                    <th>Date</th>
                    <th style="text-align:right;">Amount</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div style="width:1.75rem;height:1.75rem;border-radius:.375rem;background:linear-gradient(135deg, var(--brand-500), oklch(0.50 0.14 300));display:flex;align-items:center;justify-content:center;color:white;font-size:.55rem;font-weight:800;flex-shrink:0;">
                                {{ strtoupper(substr($q->quote_number, -2)) }}
                            </div>
                            <a href="/client/quotations/{{ $q->id }}" style="font-weight:700;color:var(--surface-800);text-decoration:none;font-size:.8125rem;">{{ $q->quote_number }}</a>
                            @if($q->isMilestone())
                                <span class="badge" style="background:oklch(0.95 0.04 300);color:oklch(0.50 0.16 300);padding:.1rem .35rem;font-size:.5rem;">MS</span>
                            @endif
                        </div>
                    </td>
                    <td style="color:var(--surface-500);">{{ $q->user?->company?->name ?? 'N/A' }}</td>
                    <td style="color:var(--surface-400);font-size:.75rem;">{{ $q->issue_date->format('d M Y') }}</td>
                    <td style="text-align:right;font-weight:700;color:var(--surface-800);">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                    <td style="text-align:center;"><span class="badge badge-{{ $q->status }}">{{ str_replace('_', ' ', $q->status) }}</span></td>
                    <td style="text-align:right;"><a href="/client/quotations/{{ $q->id }}" class="btn btn-ghost btn-icon" title="View" style="color:var(--brand-600);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:3rem;">
                        <div style="width:3rem;height:3rem;margin:0 auto;border-radius:.625rem;background:var(--surface-100);display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;">
                            <svg style="width:1.5rem;height:1.5rem;color:var(--surface-300);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p style="font-size:.8125rem;color:var(--surface-400);font-weight:600;">No quotations found</p>
                        <p style="font-size:.75rem;color:var(--surface-300);margin-top:.25rem;">@if($search || $status || $companyId)Try adjusting your filters<a href="/client/quotations" style="color:var(--brand-600);text-decoration:underline;margin-left:.25rem;">Clear filters</a>@elseQuotations from your companies will appear here@endif</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($quotations->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--surface-100);">
        {{ $quotations->links() }}
    </div>
    @endif
</div>

@endsection
