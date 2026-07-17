@extends('layouts.app')
@section('title', $client->name)
@section('content')

<x-page-header title="{{ $client->name }}" subtitle="{{ $client->email }}" back="/clients">
    <x-slot name="actions">
        <a href="/clients/{{ $client->id }}/edit" class="btn btn-ghost btn-icon" title="Edit Client" style="border:1px solid var(--surface-200);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </a>
    </x-slot>
</x-page-header>

<div class="stat-grid fade-in">
    <div class="stat-card">
        <div class="stat-icon brand"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        <div><div class="stat-value">{{ $quotations->count() }}</div><div class="stat-label">Total Quotations</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></div>
        <div><div class="stat-value">{{ $client->quotations->first()?->currency_symbol ?? '$' }}{{ number_format($totalQuoted, 2) }}</div><div class="stat-label">Total Quoted</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="stat-value">{{ $client->quotations->first()?->currency_symbol ?? '$' }}{{ number_format($totalPaid, 2) }}</div><div class="stat-label">Total Paid</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><div class="stat-value">{{ $pendingPayments }}</div><div class="stat-label">Pending Payments</div></div>
    </div>
</div>

<div class="grid-2-1" style="margin-top:1rem;">
    <x-card>
        <div class="d-card-header">
            <h3>Quotations</h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="d-table">
                <thead>
                    <tr>
                        <th>Quote #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td style="font-weight:600;"><a href="/quotations/{{ $q->id }}" style="color:var(--brand-600);text-decoration:none;">{{ $q->quote_number }}</a></td>
                        <td>{{ $q->issue_date->format('M d, Y') }}</td>
                        <td style="font-weight:600;">{{ $q->currency_symbol }}{{ number_format($q->grand_total, 2) }}</td>
                        <td><x-status-badge :status="$q->status">{{ ucfirst($q->status) }}</x-status-badge></td>
                        <td>
                            @if($q->payment_status === 'paid')
                                <span class="badge badge-accepted">Paid</span>
                            @elseif($q->payment_status === 'partial')
                                <span class="badge badge-opened">Partial</span>
                            @else
                                <span class="badge badge-declined">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state icon="quote" title="No quotations yet." description="Create your first quotation for this client." action="/quotations/create?client_id={{ $client->id }}" actionLabel="+ New Quotation" />
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div style="display:flex;flex-direction:column;gap:1rem;">
        <x-card>
            <div class="d-card-header">
                <h3>Contact Details</h3>
            </div>
            <div style="padding:1.25rem;">
                <div style="display:flex;flex-direction:column;gap:.75rem;font-size:.8125rem;">
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.125rem;">Name</div>
                        <div style="font-weight:600;color:var(--surface-800);">{{ $client->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.125rem;">Email</div>
                        <div style="font-weight:600;color:var(--surface-800);">{{ $client->email }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.125rem;">Phone</div>
                        <div style="font-weight:600;color:var(--surface-800);">{{ $client->phone ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.125rem;">Address</div>
                        <div style="font-weight:600;color:var(--surface-800);">{{ $client->address ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--surface-500);margin-bottom:.125rem;">Client Since</div>
                        <div style="font-weight:600;color:var(--surface-800);">{{ $client->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </x-card>

        <a href="/quotations/create?client_id={{ $client->id }}" class="btn btn-brand" style="text-align:center;justify-content:center;">+ New Quotation</a>
    </div>
</div>
@endsection
