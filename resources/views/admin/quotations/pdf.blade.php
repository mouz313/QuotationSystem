@php
    $company = $company ?? ($quotation->user->company ?? null);
    $brandColor = $company?->brand_color ?? '#4f46e5';
    $cs = $quotation->currency_symbol;
    $totalPaid = $quotation->paid_amount ?? 0;
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

        /* HEADER */
        .header-bar { background: {{ $brandColor }}; padding: 24px 30px; color: #fff; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .company-logo { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; margin-right: 14px; border: 2px solid rgba(255,255,255,0.3); }
        .company-name { font-size: 20px; font-weight: bold; margin: 0 0 3px 0; color: #fff; }
        .company-info-line { font-size: 9px; color: rgba(255,255,255,0.8); margin: 1px 0; }
        .quote-label { text-align: right; }
        .quote-label h1 { font-size: 26px; font-weight: bold; color: #fff; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
        .quote-label .qnum { font-size: 12px; color: rgba(255,255,255,0.85); margin-top: 4px; }

        /* META SECTION */
        .meta-section { padding: 16px 30px; border-bottom: 1px solid #e2e8f0; }
        .meta-grid { width: 100%; }
        .meta-grid td { padding: 4px 0; vertical-align: top; }
        .meta-label { font-size: 8px; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.8px; font-weight: bold; margin-bottom: 2px; }
        .meta-value { font-size: 11px; font-weight: 600; color: #2d3748; }

        /* STATUS BADGE */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-draft { background: #edf2f7; color: #718096; }
        .status-sent { background: #bee3f8; color: #2b6cb0; }
        .status-opened { background: #c3dafe; color: #4c51bf; }
        .status-accepted { background: #c6f6d5; color: #276749; }
        .status-declined { background: #fed7d7; color: #c53030; }
        .status-change_requested { background: #fefcbf; color: #b7791f; }
        .badge-paid { background: #c6f6d5; color: #276749; }
        .badge-partial { background: #fefcbf; color: #b7791f; }
        .badge-unpaid { background: #edf2f7; color: #718096; }

        /* BILL TO / PREPARED BY */
        .party-section { padding: 14px 30px; background: #f7fafc; border-bottom: 1px solid #e2e8f0; }
        .party-grid { width: 100%; }
        .party-grid td { padding: 4px 0; vertical-align: top; }
        .party-name { font-size: 12px; font-weight: bold; color: #2d3748; }
        .party-detail { font-size: 10px; color: #718096; }

        /* ITEMS TABLE */
        .items-section { padding: 16px 30px; }
        .items-section h3 { font-size: 9px; text-transform: uppercase; color: #a0aec0; letter-spacing: 0.8px; margin-bottom: 8px; font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: {{ $brandColor }}; color: #fff; padding: 8px 10px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #edf2f7; font-size: 10px; color: #4a5568; }
        table.items tbody tr:nth-child(even) { background: #f7fafc; }
        table.items th:last-child, table.items td:last-child { text-align: right; }
        table.items th.text-right, table.items td.text-right { text-align: right; }

        /* TOTALS */
        .totals-box { margin: 10px 30px 16px 30px; float: right; width: 250px; }
        .totals-box table { width: 100%; border-collapse: collapse; }
        .totals-box td { padding: 4px 6px; font-size: 10px; }
        .totals-box .tlabel { color: #718096; text-align: left; }
        .totals-box .tvalue { text-align: right; font-weight: 600; color: #2d3748; }
        .totals-box .grand td { border-top: 2px solid {{ $brandColor }}; padding-top: 6px; font-size: 13px; font-weight: bold; color: {{ $brandColor }}; }
        .clear { clear: both; }

        /* SECTIONS */
        .section-block { padding: 12px 30px; border-top: 1px solid #e2e8f0; }
        .section-block h4 { font-size: 8px; text-transform: uppercase; color: #a0aec0; margin-bottom: 6px; letter-spacing: 0.8px; font-weight: bold; }
        .section-block p { font-size: 10px; color: #4a5568; line-height: 1.6; white-space: pre-wrap; }

        /* ACCOUNT DETAILS */
        .account-box { background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 6px; padding: 12px 16px; }
        .account-box h4 { color: #276749; margin-bottom: 6px; }
        .account-box p { color: #2d3748; font-size: 10px; line-height: 1.6; white-space: pre-wrap; }

        /* PAYMENT SUMMARY */
        .payment-box { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin: 4px 0; }
        .payment-box table { width: 100%; }
        .payment-box td { padding: 3px 0; font-size: 10px; }
        .pay-label { color: #a0aec0; }
        .pay-value { font-weight: 600; color: #2d3748; text-align: right; }

        /* MILESTONE */
        .milestone-badge { background: #ede9fe; color: #6d28d9; padding: 2px 8px; border-radius: 8px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }

        /* FOOTER */
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
                                @if($company->website)<div class="company-info-line">{{ $company->website }}</div>@endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="quote-label">
                    <h1>Quotation</h1>
                    <div class="qnum">{{ $quotation->quote_number }}</div>
                    @if($quotation->isMilestone())<div style="margin-top:4px;"><span class="milestone-badge">Milestone</span></div>@endif
                </td>
            </tr>
        </table>
    </div>

    {{-- META --}}
    <div class="meta-section">
        <table class="meta-grid">
            <tr>
                <td style="width:18%;">
                    <div class="meta-label">Issue Date</div>
                    <div class="meta-value">{{ $quotation->issue_date->format('d M, Y') }}</div>
                </td>
                <td style="width:18%;">
                    <div class="meta-label">Expiry Date</div>
                    <div class="meta-value">{{ $quotation->expiry_date ? $quotation->expiry_date->format('d M, Y') : 'N/A' }}</div>
                </td>
                <td style="width:18%;">
                    <div class="meta-label">Currency</div>
                    <div class="meta-value">{{ $quotation->currency->code ?? 'N/A' }}</div>
                </td>
                <td style="width:18%;">
                    <div class="meta-label">Status</div>
                    <div class="meta-value"><span class="badge status-{{ $quotation->status }}">{{ str_replace('_', ' ', ucfirst($quotation->status)) }}</span></div>
                </td>
                <td style="width:18%;">
                    <div class="meta-label">Payment</div>
                    <div class="meta-value"><span class="badge badge-{{ $pStatus }}">{{ ucfirst($pStatus) }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- BILL TO / PREPARED BY --}}
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
                    <div class="meta-label">Prepared By</div>
                    <div class="party-name">{{ $quotation->user->name }}</div>
                    @if($company)<div class="party-detail">{{ $company->name }}</div>@endif
                </td>
            </tr>
        </table>
    </div>

    {{-- PAYMENT SUMMARY (if has payments) --}}
    @if($totalPaid > 0)
    <div class="section-block" style="border-top:none; padding-bottom:0;">
        <div class="payment-box">
            <table>
                <tr>
                    <td class="pay-label">Payment Status:</td>
                    <td class="pay-value"><span class="badge badge-{{ $pStatus }}">{{ ucfirst($pStatus) }}</span></td>
                </tr>
                <tr>
                    <td class="pay-label">Total Paid:</td>
                    <td class="pay-value" style="color:#276749;">{{ $cs }}{{ number_format($totalPaid, 2) }}</td>
                </tr>
                @if($remaining > 0)
                <tr>
                    <td class="pay-label">Balance Due:</td>
                    <td class="pay-value" style="color:#c53030;">{{ $cs }}{{ number_format($remaining, 2) }}</td>
                </tr>
                @endif
                @if($quotation->paid_at)
                <tr>
                    <td class="pay-label">Last Payment:</td>
                    <td class="pay-value">{{ $quotation->paid_at->format('d M, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ITEMS TABLE --}}
    <div class="items-section">
        <h3>{{ $quotation->isMilestone() ? 'Milestones' : 'Line Items' }}</h3>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:22%;">{{ $quotation->isMilestone() ? 'Milestone' : 'Item' }}</th>
                    <th style="width:28%;">Description</th>
                    @if($quotation->isMilestone())
                    <th style="width:15%;">Start Date</th>
                    <th style="width:15%;">End Date</th>
                    @endif
                    <th class="text-right" style="width:8%;">Qty</th>
                    <th class="text-right" style="width:12%;">Unit Price</th>
                    <th class="text-right" style="width:12%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items()->orderBy('sort_order')->get() as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->item_title }}</strong></td>
                    <td>{{ $item->item_description ?? '-' }}</td>
                    @if($quotation->isMilestone())
                    <td>{{ $item->start_date ? $item->start_date->format('d M Y') : '-' }}</td>
                    <td>{{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}</td>
                    @endif
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $cs }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right" style="font-weight:600;">{{ $cs }}{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TOTALS --}}
    <div class="totals-box">
        <table>
            <tr>
                <td class="tlabel">Subtotal</td>
                <td class="tvalue">{{ $cs }}{{ number_format($quotation->items->sum('subtotal'), 2) }}</td>
            </tr>
            @if($quotation->discount_amount > 0)
            <tr>
                <td class="tlabel">Discount</td>
                <td class="tvalue" style="color:#c53030;">-{{ $cs }}{{ number_format($quotation->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($quotation->tax_percentage > 0)
            <tr>
                <td class="tlabel">Tax ({{ $quotation->tax_percentage }}%)</td>
                <td class="tvalue">{{ $cs }}{{ number_format($quotation->items->sum('subtotal') * $quotation->tax_percentage / 100, 2) }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td class="tlabel">Grand Total</td>
                <td class="tvalue">{{ $cs }}{{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    {{-- PAYMENT INSTRUCTIONS --}}
    @if($quotation->payment_instructions)
    <div class="section-block">
        <h4>Payment Instructions</h4>
        <p>{{ $quotation->payment_instructions }}</p>
    </div>
    @endif

    {{-- TERMS & CONDITIONS --}}
    @if($quotation->terms_conditions)
    <div class="section-block">
        <h4>Terms &amp; Conditions</h4>
        <p>{{ $quotation->terms_conditions }}</p>
    </div>
    @endif

    {{-- ACCOUNT DETAILS --}}
    @if($company && $company->account_details)
    <div class="section-block">
        <div class="account-box">
            <h4 style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; color:#276749;">Account / Bank Details</h4>
            <p>{{ $company->account_details }}</p>
        </div>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer-bar">
        Generated on {{ now()->format('d M Y \a\t h:i A') }} &middot; {{ $company->name ?? '' }} &middot; {{ $company->email ?? '' }}
    </div>

</div>
</body>
</html>
