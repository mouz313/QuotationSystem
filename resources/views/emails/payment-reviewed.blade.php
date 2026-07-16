@php
    $hex = ltrim($brandColor, '#');
    [$r, $g, $b] = [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
    $darkHex = '#' . substr('0' . dechex(max(0, $r - 40)), -2) . substr('0' . dechex(max(0, $g - 40)), -2) . substr('0' . dechex(max(0, $b - 40)), -2);
    $companyName = $company->name ?? config('app.name');
    $logoUrl = $company->logo_url ?? null;
    $isApproved = strtolower($paymentStatus) === 'approved';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment {{ $paymentStatus }}</title>
    <style>
        body { margin:0; padding:0; background:#f4f6f9; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width:600px; margin:0 auto; padding:40px 20px; }
        .card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg, {{ $brandColor }}, {{ $darkHex }}); padding:32px 40px; text-align:center; }
        .header h1 { margin:0; color:#fff; font-size:22px; }
        .header p { margin:8px 0 0; color:rgba(255,255,255,0.85); font-size:14px; }
        .body { padding:40px; }
        .info { background:#f9fafb; border-radius:8px; padding:20px 24px; margin-bottom:24px; }
        .info p { margin:8px 0; font-size:14px; color:#374151; }
        .info strong { color:#111827; }
        .amount-display { font-size:28px; font-weight:700; text-align:center; margin:16px 0; }
        .amount-display.approved { color:#059669; }
        .amount-display.rejected { color:#dc2626; }
        .btn { display:inline-block; padding:12px 32px; background:{{ $brandColor }}; color:#fff !important; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; }
        .note-box { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin:16px 0; font-size:13px; color:#6b7280; }
        .footer-text { text-align:center; color:#9ca3af; font-size:12px; margin-top:24px; line-height:1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}" style="height:48px;margin-bottom:12px;border-radius:6px;">
                @endif
                <h1>Payment {{ $paymentStatus }}</h1>
                <p>Quote #{{ $quoteNumber }}</p>
            </div>
            <div class="body">
                <p style="font-size:15px;color:#374151;">Dear Customer,</p>
                <p style="font-size:14px;color:#6b7280;">
                    Your payment of <strong>{{ $currency }}{{ $amount }}</strong> for quotation <strong>{{ $quoteNumber }}</strong> has been <strong>{{ strtolower($paymentStatus) }}</strong>.
                </p>

                <div class="amount-display {{ strtolower($paymentStatus) }}">{{ $currency }}{{ $amount }}</div>

                <div class="info">
                    <p><strong>Quote Number:</strong> {{ $quoteNumber }}</p>
                    <p><strong>Amount:</strong> {{ $currency }}{{ $amount }}</p>
                    <p><strong>Grand Total:</strong> {{ $currency }}{{ $grandTotal }}</p>
                    <p><strong>Total Paid:</strong> {{ $currency }}{{ $totalPaid }}</p>
                    <p><strong>Status:</strong>
                        <span style="color:{{ $isApproved ? '#059669' : '#dc2626' }};font-weight:600;">
                            {{ $paymentStatus }}
                        </span>
                    </p>
                    <p><strong>Reviewed By:</strong> {{ $reviewerName }}</p>
                </div>

                @if($reviewerNotes)
                <div class="note-box">
                    <strong style="color:#374151;">Review Notes:</strong><br>
                    {{ $reviewerNotes }}
                </div>
                @endif

                @if($isApproved)
                <div style="background:#ecfdf5;border-radius:8px;padding:16px;margin-bottom:20px;text-align:center;">
                    <p style="font-size:14px;color:#059669;font-weight:600;margin:0;">
                        &#10003; Your payment has been approved and your balance updated.
                    </p>
                </div>
                @endif

                <div style="text-align:center;margin:24px 0;">
                    <a href="{{ url('/client/login') }}" class="btn">View in Client Portal</a>
                </div>
            </div>
        </div>
        <div class="footer-text">{{ $companyName }} &mdash; All rights reserved.</div>
    </div>
</body>
</html>
