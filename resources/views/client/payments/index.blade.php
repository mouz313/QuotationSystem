@extends('client.layouts.client')
@section('title', 'Payment History')
@section('header-title', 'Client Portal')
@section('header-sub', 'Payment History')
@section('content')

<x-page-header title="Payment History" subtitle="{{ $payments->total() }} payment{{ $payments->total() !== 1 ? 's' : '' }}" />

<div class="stat-grid fade-in" style="margin-bottom:1rem;grid-template-columns:repeat(3, 1fr);">
    <div class="stat-card" style="border-color:oklch(0.88 0.04 150);">
        <div class="stat-icon success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--success-600);">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved</div>
        </div>
    </div>
    <div class="stat-card" style="border-color:oklch(0.88 0.06 80);">
        <div class="stat-icon warning">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--warning-600);">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="stat-card" style="border-color:oklch(0.88 0.04 25);">
        <div class="stat-icon danger">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--danger-600);">{{ $stats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>
</div>

<div class="d-card fade-in fade-in-1">
    <form method="GET" action="/client/payments" style="padding:1rem 1.25rem;border-bottom:1px solid var(--surface-100);display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <select name="status" onchange="this.form.submit()" style="padding:.5rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;min-width:140px;">
            <option value="">All Statuses</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        @if($status)
            <a href="/client/payments" style="padding:.5rem .75rem;font-size:.8125rem;font-weight:600;color:var(--danger-600);border:1px solid var(--danger-200);border-radius:.5rem;text-decoration:none;transition:background .15s;white-space:nowrap;" onmouseover="this.style.background='var(--danger-50)'" onmouseout="this.style.background='transparent'">Clear</a>
        @endif
    </form>
    <div class="overflow-x-auto">
        <table class="d-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Quote #</th>
                    <th>Company</th>
                    <th style="text-align:right;">Amount</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Proof</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td style="color:var(--surface-400);font-size:.75rem;">{{ $p->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="/client/quotations/{{ $p->quotation_id }}" style="font-weight:700;color:var(--surface-800);text-decoration:none;font-size:.8125rem;">{{ $p->quotation?->quote_number ?? 'N/A' }}</a>
                    </td>
                    <td style="color:var(--surface-500);">{{ $p->quotation?->user?->company?->name ?? 'N/A' }}</td>
                    <td style="text-align:right;font-weight:700;color:var(--surface-800);">{{ $p->quotation?->currency_symbol }}{{ number_format($p->amount, 2) }}</td>
                    <td style="text-align:center;">
                        @php
                            $badgeStyle = match($p->status) {
                                'approved' => 'background:var(--success-100);color:var(--success-700)',
                                'rejected' => 'background:var(--danger-100);color:var(--danger-700)',
                                default => 'background:var(--warning-100);color:var(--warning-700)',
                            };
                        @endphp
                        <span style="padding:.125rem .5rem;font-size:.5625rem;font-weight:700;border-radius:9999px;text-transform:uppercase;letter-spacing:.05em;{{ $badgeStyle }}">{{ $p->status }}</span>
                    </td>
                    <td style="text-align:right;">
                        @if($p->proof)
                            <a href="/storage/{{ $p->proof }}" target="_blank" class="btn btn-ghost btn-icon" title="View Proof" style="color:var(--brand-600);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        @else
                            <span style="color:var(--surface-300);font-size:.75rem;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:3rem;">
                        <div style="width:3rem;height:3rem;margin:0 auto;border-radius:.625rem;background:var(--surface-100);display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;">
                            <svg style="width:1.5rem;height:1.5rem;color:var(--surface-300);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <p style="font-size:.8125rem;color:var(--surface-400);font-weight:600;">No payments found</p>
                        <p style="font-size:.75rem;color:var(--surface-300);margin-top:.25rem;">@if($status)Try adjusting your filters<a href="/client/payments" style="color:var(--brand-600);text-decoration:underline;margin-left:.25rem;">Clear filters</a>@elseYour payment submissions will appear here@endif</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--surface-100);">
        {{ $payments->links() }}
    </div>
    @endif
</div>

@endsection
