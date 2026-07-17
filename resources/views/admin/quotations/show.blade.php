@extends('layouts.admin')
@section('title', 'Quotation ' . $quotation->quote_number)
@section('content')
<div class="fade-in">
    <x-page-header title="{{ $quotation->quote_number }}" subtitle="Issued {{ $quotation->issue_date->format('M d, Y') }} &middot; {{ $quotation->user->company?->name ?? 'N/A' }}" back="/admin/quotations" />

    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <form method="POST" action="/admin/quotations/{{ $quotation->id }}/status" style="display:flex;align-items:center;gap:.5rem;">
            @csrf @method('PATCH')
            <select name="status" style="padding:.35rem .75rem;border:1px solid var(--surface-200);border-radius:.5rem;font-size:.8125rem;color:var(--surface-800);background:var(--surface-0);outline:none;">
                <option value="draft" {{ $quotation->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $quotation->status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="accepted" {{ $quotation->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="declined" {{ $quotation->status === 'declined' ? 'selected' : '' }}>Declined</option>
            </select>
            <button type="submit" class="btn btn-brand btn-sm">Update Status</button>
        </form>
        <a href="/admin/quotations/{{ $quotation->id }}/pdf" class="btn btn-ghost btn-sm" style="border:1px solid var(--surface-200);">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <form method="POST" action="/admin/quotations/{{ $quotation->id }}" onsubmit="return confirm('Delete this quotation permanently?')" style="margin-left:auto;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-icon" title="Delete" style="color:var(--danger-600);">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </form>
    </div>

    <div class="d-card" style="margin-bottom:1.5rem;">
        <div class="d-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div>
                    <h3 style="font-size:.8125rem;font-weight:700;color:var(--surface-500);margin-bottom:.25rem;">Client</h3>
                    <div style="font-weight:600;">{{ $quotation->client->name }}</div>
                    <div style="font-size:.8125rem;color:var(--surface-600);">{{ $quotation->client->email }}</div>
                    @if($quotation->client->phone)<div style="font-size:.8125rem;color:var(--surface-600);">{{ $quotation->client->phone }}</div>@endif
                </div>
                <div style="text-align:right;">
                    <div style="margin-bottom:.5rem;">
                        <x-quotation-status-badge :status="$quotation->status" />
                    </div>
                    <div style="font-size:.8125rem;color:var(--surface-500);">Expiry: {{ $quotation->expiry_date?->format('M d, Y') ?? 'N/A' }}</div>
                    @if($quotation->currency)<div style="font-size:.8125rem;color:var(--surface-500);margin-top:.25rem;">Currency: {{ $quotation->currency->symbol }} {{ $quotation->currency->code }}</div>@endif
                    <div style="font-size:.8125rem;color:var(--surface-500);margin-top:.25rem;">Created by: {{ $quotation->user->name }}</div>
                </div>
            </div>
            <table class="d-table" style="margin-bottom:1.5rem;">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td style="font-weight:600;">{{ $item->item_title }}</td>
                        <td style="color:var(--surface-600);">{{ $item->item_description ?? '-' }}</td>
                        <td style="text-align:right;">{{ $item->quantity }}</td>
                        <td style="text-align:right;">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align:right;font-weight:600;">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="display:flex;justify-content:flex-end;">
                <div style="width:16rem;display:flex;flex-direction:column;gap:.5rem;font-size:.8125rem;">
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Subtotal:</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</span></div>
                    @if($quotation->discount_amount > 0)<div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Discount:</span><span style="color:var(--danger-600);">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>@endif
                    @if($quotation->tax_percentage > 0)<div style="display:flex;justify-content:space-between;"><span style="color:var(--surface-500);">Tax ({{ $quotation->tax_percentage }}%):</span><span>{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</span></div>@endif
                    <div style="display:flex;justify-content:space-between;border-top:1px solid var(--surface-200);padding-top:.5rem;font-size:1.125rem;font-weight:800;"><span>Grand Total:</span><span style="color:var(--brand-600);">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</span></div>
                </div>
            </div>
            @if($quotation->terms_conditions)
                <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--surface-100);">
                    <h4 style="font-size:.8125rem;font-weight:700;color:var(--surface-500);margin-bottom:.25rem;">Terms & Conditions</h4>
                    <p style="font-size:.8125rem;color:var(--surface-600);">{{ $quotation->terms_conditions }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
