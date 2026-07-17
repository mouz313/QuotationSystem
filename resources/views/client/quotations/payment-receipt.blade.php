@php
    $company = $company ?? ($quotation->user->company ?? null);
    $brandColor = $company?->brand_color ?? '#4f46e5';
    $cs = $quotation->currency_symbol;
    $approvedPayments = $quotation->payments->where('status', 'approved');
    $totalPaid = $approvedPayments->sum('amount');
    $remaining = max(0, $quotation->grand_total - $totalPaid);
    $pStatus = $quotation->payment_status ?? 'unpaid';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', 'DejaVuSans', sans-serif; color: #2d3748; font-size: 10px; line-height: 1.5; }
        .page { padding: 0; }

        .header-bar { background: {{ $brandColor }}; padding: 24px 30px; color: #fff; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .company-logo { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; margin-right: 14px; border: 2px solid rgba(255,255,255,0.3); }
        .company-name { font-size: 20px; font-weight: bold; margin: 0 0 3px 0; color: #fff; }
        .company-info-line { font-size: 9px; color: rgba(255,255,255,0.8); margin: 1px 0; }
        .receipt-label { text-align: right; }
        .receipt-label h1 { font-size: 26px; font-weight: bold; color: #fff; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
        .receipt-label .rnum { font-size: 12px; color: rgba(255,255,255,0.85); margin-top: 4px; }
        .receipt-label .rdate { font-size: 10px; color: rgba(255,255,255,0.7); margin-top: 2px; }

        .meta-section { padding: 16px 30px; border-bottom: 1px solid #e2e8f0; }
        .meta-grid { width: 100%; }
        .meta-grid td { padding: 4px 0; vertical-align: top; }
        .meta-label { font-size: 8px; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.8px; font-weight: bold; margin-bottom: 2px; }
        .meta-value { font-size: 11px; font-weight: 600; color: #2d3748; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-paid { background: #c6f6d5; color: #276749; }
        .badge-partial { background: #fefcbf; color: #b7791f; }
        .badge-unpaid { background: #edf2f7; color: #718096; }

        .party-section { padding: 14px 30px; background: #f7fafc; border-bottom: 1px solid #e2e8f0; }
        .party-grid { width: 100%; }
        .party-grid td { padding: 4px 0; vertical-align: top; }
        .party-name { font-size: 12px; font-weight: bold; color: #2d3748; }
        .party-detail { font-size: 10px; color: #718096; }

        .section-block { padding: 12px 30px; border-top: 1px solid #e2e8f0; }
        .section-block h4 { font-size: 8px; text-transform: uppercase; color: #a0aec0; margin-bottom: 6px; letter-spacing: 0.8px; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: {{ $brandColor }}; color: #fff; padding: 8px 10px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #edf2f7; font-size: 10px; color: #4a5568; }
        table.items th:last-child, table.items td:last-child { text-align: right; }
        table.items th.text-right, table.items td.text-right { text-align: right; }

        .totals-box { margin: 10px 30px 16px 30px; float: right; width: 250px; }
        .totals-box table { width: 100%; border-collapse: collapse; }
        .totals-box td { padding: 4px 6px; font-size: 10px; }
        .totals-box .tlabel { color: #718096; text-align: left; }
        .totals-box .tvalue { text-align: right; font-weight: 600; color: #2d3748; }
        .totals-box .grand td { border-top: 2px solid {{ $brandColor }}; padding-top: 6px; font-size: 13px; font-weight: bold; color: {{ $brandColor }}; }
        .clear { clear: both; }

        .payment-box { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin: 0 30px 16px 30px; }
        .payment-box table { width: 100%; }
        .payment-box td { padding: 3px 0; font-size: 10px; }
        .pay-label { color: #a0aec0; }
        .pay-value { font-weight: 600; color: #2d3748; text-align: right; }

        .account-box { background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 6px; padding: 12px 16px; }
        .account-box h4 { color: #276749; margin-bottom: 6px; }
        .account-box p { color: #2d3748; font-size: 10px; line-height: 1.6; white-space: pre-wrap; }

        .footer-bar { margin-top: 24px; padding: 12px 30px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 8px; color: #a0aec0; background: #f7fafc; }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header-bar">
        <table class="header-table">
            <tr>
                <td>
                    <table>
                        <tr>
                            @if($company && $company->logo)
                            <td style="vertical-align:middle; padding-right:14px;">
                                <img src="{{ storage_path('app/public/' . $company->logo) }}" class="company-logo">
                            </td>
                            @endif
                            <td style="vertical-align:middle;">
                                <div class="company-name">{{ $company->name ?? 'Company' }}</div>
                                @if($company->email)<div class="company-info-line">{{ $company->email }}</div>@endif
                                @if($company->phone)<div class="company-info-line">{{ $company->phone }}</div>@endif
                                @if($company->address)<div class="company-info-line">{{ $company->address }}</div>@endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="receipt-label">
                    <h1>Payment Receipt</h1>
                    <div class="rnum">Ref: {{ $quotation->quote_number }}-RCPT</div>
                    <div class="rdate">{{ now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- META --}}
    <div class="meta-section">
        <table class="meta-grid">
            <tr>
                <td style="width:20%;">
                    <div class="meta-label">Quotation</div>
                    <div class="meta-value">{{ $quotation->quote_number }}</div>
                </td>
                @if($quotation->invoice_number)
                <td style="width:20%;">
                    <div class="meta-label">Invoice #</div>
                    <div class="meta-value">{{ $quotation->invoice_number }}</div>
                </td>
                @endif
                <td style="width:20%;">
                    <div class="meta-label">Issue Date</div>
                    <div class="meta-value">{{ $quotation->issue_date->format('d M, Y') }}</div>
                </td>
                <td style="width:20%;">
                    <div class="meta-label">Currency</div>
                    <div class="meta-value">{{ $quotation->currency->code ?? 'N/A' }}</div>
                </td>
                <td style="width:20%;">
                    <div class="meta-label">Payment Status</div>
                    <div class="meta-value"><span class="badge badge-{{ $pStatus }}">{{ ucfirst($pStatus) }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- BILL TO / RECEIVED FROM --}}
    <div class="party-section">
        <table class="party-grid">
            <tr>
                <td style="width:50%; padding-right:20px;">
                    <div class="meta-label">Bill To</div>
                    <div class="party-name">{{ $quotation->client->name }}</div>
                    @if($quotation->client->email)<div class="party-detail">{{ $quotation->client->email }}</div>@endif
                    @if($quotation->client->phone)<div class="party-detail">{{ $quotation->client->phone }}</div>@endif
                </td>
                <td style="width:50%;">
                    <div class="meta-label">Received From</div>
                    @if($company)
                    <div class="party-name">{{ $company->name }}</div>
                    @if($company->email)<div class="party-detail">{{ $company->email }}</div>@endif
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- PAYMENT TRANSACTIONS --}}
    <div class="section-block">
        <h4>Payment Transactions</h4>
    </div>
    <div style="padding: 0 30px 16px 30px;">
        <table class="items">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:15%;">Date</th>
                    <th style="width:15%;">Amount</th>
                    @if($quotation->isMilestone())
                    <th style="width:20%;">Milestone</th>
                    @endif
                    <th style="width:20%;">Submitted By</th>
                    <th style="width:15%;">Status</th>
                    <th style="width:10%;">Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvedPayments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->reviewed_at ? $payment->reviewed_at->format('d M Y') : $payment->created_at->format('d M Y') }}</td>
                    <td style="font-weight:600; color:#276749;">{{ $cs }}{{ number_format($payment->amount, 2) }}</td>
                    @if($quotation->isMilestone())
                    <td>{{ $payment->quotationItem?->item_title ?? '-' }}</td>
                    @endif
                    <td>{{ $payment->clientUser->name }}</td>
                    <td><span class="badge badge-paid">Approved</span></td>
                    <td>PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAYMENT SUMMARY --}}
    <div class="section-block">
        <h4>Payment Summary</h4>
    </div>
    <div class="payment-box">
        <table>
            <tr>
                <td class="pay-label">Grand Total:</td>
                <td class="pay-value" style="font-size:12px;">{{ $cs }}{{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td class="pay-label">Total Paid (Approved):</td>
                <td class="pay-value" style="color:#276749;">{{ $cs }}{{ number_format($totalPaid, 2) }}</td>
            </tr>
            @if($remaining > 0)
            <tr>
                <td class="pay-label">Balance Due:</td>
                <td class="pay-value" style="color:#c53030;">{{ $cs }}{{ number_format($remaining, 2) }}</td>
            </tr>
            @else
            <tr>
                <td class="pay-label" style="font-weight:bold;">Status:</td>
                <td class="pay-value" style="color:#276749; font-weight:bold;">Fully Paid</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ACCOUNT DETAILS --}}
    @if($company && $company->account_details)
    <div class="section-block">
        <div class="account-box">
            <h4 style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; color:#276749;">Account / Bank Details</h4>
            <p>{{ $company->account_details }}</p>
        </div>
    </div>
    @endif

    {{-- PAYMENT INSTRUCTIONS --}}
    @if($quotation->payment_instructions)
    <div class="section-block">
        <h4>Payment Instructions</h4>
        <p style="font-size:10px; color:#4a5568; white-space:pre-wrap; line-height:1.6;">{{ $quotation->payment_instructions }}</p>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer-bar">
        This receipt was generated on {{ now()->format('d M Y \a\t h:i A') }} &middot; {{ $company->name ?? '' }} &middot; {{ $company->email ?? '' }}
    </div>

</div>
</body>
</html>
