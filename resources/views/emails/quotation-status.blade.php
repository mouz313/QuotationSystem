<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Status Update</title>
    <style>
        body { margin:0; padding:0; background:#f4f6f9; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width:600px; margin:0 auto; padding:40px 20px; }
        .card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg, #4f46e5, #7c3aed); padding:32px 40px; text-align:center; }
        .header h1 { margin:0; color:#fff; font-size:22px; }
        .header p { margin:8px 0 0; color:rgba(255,255,255,0.85); font-size:14px; }
        .body { padding:40px; }
        .info { background:#f9fafb; border-radius:8px; padding:20px 24px; margin-bottom:24px; }
        .info p { margin:8px 0; font-size:14px; color:#374151; }
        .info strong { color:#111827; }
        .status-badge { display:inline-block; padding:4px 14px; border-radius:12px; font-size:13px; font-weight:600; }
        .status-accepted { background:#d1fae5; color:#059669; }
        .status-declined { background:#fee2e2; color:#dc2626; }
        .status-change_requested { background:#fef3c7; color:#d97706; }
        .status-sent { background:#dbeafe; color:#2563eb; }
        .status-opened { background:#e0e7ff; color:#4f46e5; }
        .btn { display:inline-block; padding:12px 32px; background:#4f46e5; color:#fff !important; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; }
        .btn-green { background:#059669; }
        .btn-amber { background:#d97706; }
        .note-box { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin:16px 0; font-size:13px; color:#6b7280; }
        .footer-text { text-align:center; color:#9ca3af; font-size:12px; margin-top:24px; line-height:1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>Quotation Status Update</h1>
                <p>Quote #{{ $quoteNumber }}</p>
            </div>
            <div class="body">
                @if($recipientType === 'company')
                    <p style="font-size:15px;color:#374151;">Dear Team,</p>
                    <p style="font-size:14px;color:#6b7280;">
                        <strong>{{ $changedByName }}</strong> has updated quotation <strong>{{ $quoteNumber }}</strong>.
                    </p>
                @else
                    <p style="font-size:15px;color:#374151;">Dear {{ $clientName }},</p>
                    <p style="font-size:14px;color:#6b7280;">
                        The status of quotation <strong>{{ $quoteNumber }}</strong> has been updated by <strong>{{ $changedByName }}</strong>.
                    </p>
                @endif

                <div class="info">
                    <p>
                        <strong>Previous Status:</strong>
                        <span class="status-badge status-{{ $oldStatus ?? 'draft' }}" style="opacity:0.7;">{{ $oldStatus ?? 'N/A' }}</span>
                        &nbsp;&rarr;&nbsp;
                        <strong>New Status:</strong>
                        <span class="status-badge status-{{ $newStatus }}">{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</span>
                    </p>
                    <p><strong>Quote Number:</strong> {{ $quoteNumber }}</p>
                    <p><strong>Grand Total:</strong> <span style="font-size:18px;color:#4f46e5;font-weight:700;">{{ $currency }}{{ $grandTotal }}</span></p>
                </div>

                @if($notes)
                <div class="note-box">
                    <strong style="color:#374151;">Notes:</strong><br>
                    {{ $notes }}
                </div>
                @endif

                <div style="text-align:center;margin:24px 0;">
                    @if($recipientType === 'company')
                        <a href="{{ url('/quotations/' . $quotation->id) }}" class="btn">View in Dashboard</a>
                    @else
                        <a href="{{ url('/client/login') }}" class="btn">View in Client Portal</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="footer-text">{{ config('app.name') }} &mdash; All rights reserved.</div>
    </div>
</body>
</html>
