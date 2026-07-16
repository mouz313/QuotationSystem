@php
    $hex = ltrim($brandColor, '#');
    [$r, $g, $b] = [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
    $darkHex = '#' . substr('0' . dechex(max(0, $r - 40)), -2) . substr('0' . dechex(max(0, $g - 40)), -2) . substr('0' . dechex(max(0, $b - 40)), -2);
    $companyName = $company->name ?? config('app.name');
    $logoUrl = $company->logo_url ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Client Portal</title>
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
        .btn { display:inline-block; padding:12px 32px; background:{{ $brandColor }}; color:#fff !important; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; }
        .footer-text { text-align:center; color:#9ca3af; font-size:12px; margin-top:24px; line-height:1.6; }
        .password-box { background:#fff; border:2px dashed #d1d5db; border-radius:8px; padding:16px; text-align:center; margin:16px 0; }
        .password-box code { font-size:18px; font-weight:700; color:{{ $brandColor }}; letter-spacing:1px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}" style="height:48px;margin-bottom:12px;border-radius:6px;">
                @endif
                <h1>Welcome to {{ $companyName }}!</h1>
                <p>Your Client Portal Account</p>
            </div>
            <div class="body">
                <p style="font-size:15px;color:#374151;">Hello <strong>{{ $clientUser->name }}</strong>,</p>
                <p style="font-size:14px;color:#6b7280;">A quotation has been shared with you by <strong>{{ $companyName }}</strong>. You can now access your personalized client portal to view, track, and respond to all your quotations online.</p>

                <div class="info">
                    <p><strong>Portal URL:</strong><br> <a href="{{ url('/client/login') }}" style="color:{{ $brandColor }};">{{ url('/client/login') }}</a></p>
                    <p><strong>Email:</strong><br> {{ $clientUser->email }}</p>
                    <p><strong>Temporary Password:</strong></p>
                    <div class="password-box"><code>{{ $tempPassword }}</code></div>
                    <p style="font-size:13px;color:#9ca3af;">Please change your password after logging in.</p>
                </div>

                @if($quoteNumber)
                <p style="font-size:14px;color:#6b7280;text-align:center;">You have a new quotation: <strong>{{ $quoteNumber }}</strong></p>
                @endif

                <div style="text-align:center;margin:24px 0;">
                    <a href="{{ url('/client/login') }}" class="btn">Access Client Portal</a>
                </div>

                <p style="font-size:13px;color:#9ca3af;text-align:center;">If you didn't expect this email, please ignore it.</p>
            </div>
        </div>
        <div class="footer-text">{{ $companyName }} &mdash; All rights reserved.</div>
    </div>
</body>
</html>
