@extends('layouts.admin')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="fade-in">
    <div class="toolbar">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <a href="/admin/quotations" class="btn btn-ghost btn-sm" style="border:1px solid var(--gray-200);">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <h1 style="font-size:1.25rem;font-weight:800;color:var(--gray-900);letter-spacing:-0.02em;font-family:'SF Mono','Fira Code',monospace;">{{ $quotation->quote_number }}</h1>
                    <x-quotation-status-badge :status="$quotation->status" />
                </div>
                <p style="font-size:.8125rem;color:var(--gray-400);margin-top:.125rem;">Issued {{ $quotation->issue_date->format('M d, Y') }} &middot; {{ $quotation->user->company?->name ?? 'N/A' }}</p>
            </div>
        </div>
        <div style="display:flex;gap:.375rem;">
            <form method="POST" action="/admin/quotations/{{ $quotation->id }}/status" style="display:flex;align-items:center;gap:.375rem;">
                @csrf @method('PATCH')
                <select name="status" class="form-select" style="width:auto;padding:.35rem .75rem;font-size:.8125rem;">
                    <option value="draft" {{ $quotation->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ $quotation->status === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ $quotation->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="declined" {{ $quotation->status === 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
                <button type="submit" class="btn btn-brand btn-sm">Update</button>
            </form>
            <a href="/admin/quotations/{{ $quotation->id }}/pdf" class="btn btn-outline btn-sm">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
            <form method="POST" action="/admin/quotations/{{ $quotation->id }}" onsubmit="return confirm('Delete this quotation permanently?')" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-icon" title="Delete" style="color:var(--red-500);">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:1.5rem;">
                <div>
                    <div style="font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.5rem;">Client</div>
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        <div class="avatar avatar-brand" style="font-size:.6rem;">{{ strtoupper(substr($quotation->client->name, 0, 2)) }}</div>
                        <div>
                            <div style="font-weight:600;color:var(--gray-900);">{{ $quotation->client->name }}</div>
                            <div style="font-size:.8125rem;color:var(--gray-500);">{{ $quotation->client->email }}</div>
                            @if($quotation->client->phone)
                                <div style="font-size:.8125rem;color:var(--gray-400);">{{ $quotation->client->phone }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="display:flex;flex-direction:column;gap:.25rem;font-size:.8125rem;">
                        <div style="color:var(--gray-500);">Expiry: <strong style="color:var(--gray-800);">{{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</strong></div>
                        @if($quotation->currency)
                            <div style="color:var(--gray-500);">Currency: <strong style="color:var(--gray-800);">{{ $quotation->currency->symbol }} {{ $quotation->currency->code }}</strong></div>
                        @endif
                        <div style="color:var(--gray-500);">Created by: <strong style="color:var(--gray-800);">{{ $quotation->user->name }}</strong></div>
                    </div>
                </div>
            </div>

            <table class="d-table" style="margin-bottom:1.5rem;">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>
                            <div class="cell-main">{{ $item->item_title }}</div>
                        </td>
                        <td>
                            <span style="color:var(--gray-500);">{{ $item->item_description ?? '—' }}</span>
                        </td>
                        <td style="text-align:right;font-variant-numeric:tabular-nums;">{{ $item->quantity }}</td>
                        <td style="text-align:right;font-variant-numeric:tabular-nums;">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align:right;font-weight:700;font-variant-numeric:tabular-nums;">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="display:flex;justify-content:flex-end;">
                <div style="width:18rem;display:flex;flex-direction:column;gap:.5rem;font-size:.8125rem;">
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--gray-500);">Subtotal</span>
                        <span style="font-variant-numeric:tabular-nums;">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span>
                    </div>
                    @if($quotation->discount_amount > 0)
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--gray-500);">Discount</span>
                            <span style="color:var(--red-600);font-variant-numeric:tabular-nums;">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($quotation->tax_percentage > 0)
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--gray-500);">Tax ({{ $quotation->tax_percentage }}%)</span>
                            <span style="font-variant-numeric:tabular-nums;">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span>
                        </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding-top:.75rem;border-top:2px solid var(--gray-200);font-size:1.125rem;font-weight:800;">
                        <span>Grand Total</span>
                        <span style="color:var(--brand-600);font-variant-numeric:tabular-nums;">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($quotation->terms_conditions)
                <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--gray-100);">
                    <div style="font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-500);margin-bottom:.375rem;">Terms & Conditions</div>
                    <p style="font-size:.8125rem;color:var(--gray-600);line-height:1.6;">{{ $quotation->terms_conditions }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
