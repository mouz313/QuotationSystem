@php
    $company = $company ?? ($quotation->user->company ?? null);
    $brandColor = $company?->brand_color ?? '#4f46e5';
    $brandFont = $company?->brand_font ?? 'Helvetica';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: '{{ $brandFont }}', 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 12px; margin: 0; padding: 20px; }
        .header { margin-bottom: 24px; border-bottom: 2px solid {{ $brandColor }}; padding-bottom: 15px; }
        .company-name { font-size: 22px; font-weight: bold; color: {{ $brandColor }}; margin: 0 0 4px 0; }
        .company-info { font-size: 11px; color: #666; margin: 2px 0; }
        .quote-title { text-align: right; }
        .quote-title h1 { font-size: 28px; color: {{ $brandColor }}; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 1px; }
        .quote-number { font-size: 13px; color: #555; letter-spacing: 0.3px; }
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; padding: 6px 0; }
        .meta-label { font-size: 9px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .meta-value { font-size: 12px; font-weight: 600; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        .status-draft { background: #f3f4f6; color: #6b7280; }
        .status-sent { background: #dbeafe; color: #2563eb; }
        .status-opened { background: #e0e7ff; color: #4f46e5; }
        .status-accepted { background: #d1fae5; color: #059669; }
        .status-declined { background: #fee2e2; color: #dc2626; }
        .status-change_requested { background: #fef3c7; color: #d97706; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { background: {{ $brandColor }}; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.items th:last-child, table.items td:last-child { text-align: right; }
        table.items th:nth-child(3), table.items td:nth-child(3),
        table.items th:nth-child(4), table.items td:nth-child(4) { text-align: right; }
        table.items td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        table.items tbody tr:nth-child(even) { background: #f9fafb; }
        .totals { width: 280px; float: right; margin-top: 6px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 4px 8px; font-size: 11px; }
        .totals .label { color: #666; text-align: left; }
        .totals .value { text-align: right; font-weight: 600; }
        .totals .grand-total td { border-top: 2px solid {{ $brandColor }}; padding-top: 7px; font-size: 14px; font-weight: bold; color: {{ $brandColor }}; }
        .clear { clear: both; }
        .section { margin-top: 20px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .section h4 { font-size: 10px; text-transform: uppercase; color: #999; margin: 0 0 8px 0; letter-spacing: 0.5px; }
        .section p { font-size: 11px; color: #555; margin: 0; line-height: 1.5; }
        .payment-info { background: #f9fafb; border-radius: 6px; padding: 12px 16px; margin: 6px 0; }
        .payment-info td { padding: 3px 0; font-size: 11px; }
        .payment-label { color: #888; }
        .payment-value { font-weight: 600; color: #333; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-paid { background: #d1fae5; color: #059669; }
        .badge-partial { background: #fef3c7; color: #d97706; }
        .badge-unpaid { background: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #bbb; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <table class="meta-table">
        <tr>
            <td style="width: 60%;">
                @if($company)
                    <table>
                        <tr>
                            <td style="vertical-align: middle; padding-right: 12px;">
                                @if($company->logo)
                                    <img src="{{ storage_path('app/public/' . $company->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                @endif
                            </td>
                            <td style="vertical-align: middle;">
                                <div class="company-name">{{ $company->name }}</div>
                                @if($company->email)<div class="company-info">{{ $company->email }}</div>@endif
                                @if($company->phone)<div class="company-info">{{ $company->phone }}</div>@endif
                                @if($company->address)<div class="company-info">{{ $company->address }}</div>@endif
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
            <td class="quote-title">
                <h1>Quotation</h1>
                <div class="quote-number">{{ $quotation->quote_number }}</div>
            </td>
        </tr>
    </table>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 25%;">
            <div class="meta-label">Issue Date</div>
            <div class="meta-value">{{ $quotation->issue_date->format('M d, Y') }}</div>
        </td>
        <td style="width: 25%;">
            <div class="meta-label">Expiry Date</div>
            <div class="meta-value">{{ $quotation->expiry_date ? $quotation->expiry_date->format('M d, Y') : 'N/A' }}</div>
        </td>
        <td style="width: 25%;">
            <div class="meta-label">Status</div>
            <div class="meta-value">
                <span class="status status-{{ $quotation->status }}">{{ str_replace('_', ' ', $quotation->status) }}</span>
            </div>
        </td>
        <td style="width: 25%;">
            <div class="meta-label">Payment</div>
            <div class="meta-value">
                <span class="badge badge-{{ $quotation->payment_status ?? 'unpaid' }}">{{ $quotation->payment_status ?? 'Unpaid' }}</span>
            </div>
        </td>
    </tr>
</table>

<table class="meta-table">
    <tr>
        <td style="width: 50%; padding-top: 0;">
            <div class="meta-label">Bill To</div>
            <div class="meta-value">{{ $quotation->client->name }}</div>
            <div style="font-size: 11px; color: #555;">{{ $quotation->client->email }}</div>
            @if($quotation->client->phone)<div style="font-size: 11px; color: #555;">{{ $quotation->client->phone }}</div>@endif
        </td>
        <td style="width: 50%; padding-top: 0;">
            <div class="meta-label">Prepared By</div>
            <div class="meta-value">{{ $quotation->user->name }}</div>
        </td>
    </tr>
</table>

@if($quotation->payment_status && $quotation->payment_status !== 'unpaid')
<div class="section" style="padding-top: 0; border-top: none;">
    <h4>Payment Summary</h4>
    <div class="payment-info">
        <table style="width: 100%;">
            <tr>
                <td class="payment-label">Payment Status:</td>
                <td class="payment-value"><span class="badge badge-{{ $quotation->payment_status }}">{{ ucfirst($quotation->payment_status) }}</span></td>
            </tr>
            @if($quotation->paid_amount > 0)
            <tr>
                <td class="payment-label">Total Paid:</td>
                <td class="payment-value">{{ $quotation->currency_symbol }}{{ number_format($quotation->paid_amount, 2) }}</td>
            </tr>
            @endif
            @php $remaining = $quotation->grand_total - ($quotation->paid_amount ?? 0); @endphp
            @if($remaining > 0)
            <tr>
                <td class="payment-label">Remaining Balance:</td>
                <td class="payment-value" style="color: #dc2626;">{{ $quotation->currency_symbol }}{{ number_format(max(0, $remaining), 2) }}</td>
            </tr>
            @endif
            @if($quotation->paid_at)
            <tr>
                <td class="payment-label">Paid On:</td>
                <td class="payment-value">{{ $quotation->paid_at->format('M d, Y') }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>
@endif

<table class="items">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Item</th>
            <th style="width: 30%;">Description</th>
            <th style="width: 10%;">Qty</th>
            <th style="width: 15%;">Unit Price</th>
            <th style="width: 15%;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><strong>{{ $item->item_title }}</strong></td>
            <td>{{ $item->item_description ?? '-' }}</td>
            <td style="text-align: right;">{{ $item->quantity }}</td>
            <td style="text-align: right;">{{ $quotation->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right; font-weight: 600;">{{ $quotation->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</td>
        </tr>
        @if($quotation->discount_amount > 0)
        <tr>
            <td class="label">Discount:</td>
            <td class="value" style="color: #dc2626;">-{{ $quotation->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if($quotation->tax_percentage > 0)
        <tr>
            <td class="label">Tax ({{ $quotation->tax_percentage }}%):</td>
            <td class="value">{{ $quotation->currency_symbol }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td class="label">Grand Total:</td>
            <td class="value">{{ $quotation->currency_symbol }}{{ number_format($quotation->grand_total, 2) }}</td>
        </tr>
    </table>
</div>

<div class="clear"></div>

@if($quotation->payment_instructions)
<div class="section">
    <h4>Payment Instructions</h4>
    <p>{{ $quotation->payment_instructions }}</p>
</div>
@endif

@if($quotation->terms_conditions)
<div class="section">
    <h4>Terms &amp; Conditions</h4>
    <p>{{ $quotation->terms_conditions }}</p>
</div>
@endif

<div class="footer">
    Generated on {{ now()->format('M d, Y \a\t h:i A') }}
</div>

</body>
</html>
