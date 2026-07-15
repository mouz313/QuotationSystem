<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px; }
        .body { padding: 40px; }
        .check-icon { width: 64px; height: 64px; margin: 0 auto 24px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .check-icon svg { width: 32px; height: 32px; stroke: #10b981; }
        h2 { margin: 0 0 8px; font-size: 20px; color: #1f2937; font-weight: 600; text-align: center; }
        .subtitle { text-align: center; color: #6b7280; font-size: 14px; margin: 0 0 32px; }
        .details { background: #f9fafb; border-radius: 8px; padding: 20px 24px; margin-bottom: 24px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; }
        .detail-value { color: #1f2937; font-weight: 500; }
        .badge { display: inline-block; background: #dbeafe; color: #2563eb; font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 10px; }
        .footer-text { text-align: center; color: #9ca3af; font-size: 12px; margin-top: 24px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>{{ $appName }}</h1>
                <p>Email Configuration Test</p>
            </div>
            <div class="body">
                <div class="check-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2>Test Email Sent Successfully</h2>
                <p class="subtitle">This email confirms that your SMTP settings are configured correctly.</p>

                <div class="details">
                    <div class="detail-row">
                        <span class="detail-label">Sent From</span>
                        <span class="detail-value">{{ $fromAddress }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sent To</span>
                        <span class="detail-value">{{ $toAddress }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Mail Driver</span>
                        <span class="detail-value">{{ $driver }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">SMTP Host</span>
                        <span class="detail-value">{{ $host }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">SMTP Port</span>
                        <span class="detail-value">{{ $port }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Encryption</span>
                        <span class="detail-value">{{ $encryption ?? 'None' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value"><span class="badge">&#10003; Delivered</span></span>
                    </div>
                </div>

                <p style="text-align:center;color:#6b7280;font-size:13px;margin:0;">
                    Sent at {{ $sentAt }}<br>
                    {{ $appName }} &mdash; All rights reserved.
                </p>
            </div>
        </div>
        <div class="footer-text">
            This is an automated test email from {{ $appName }}.<br>
            If you received this in error, please ignore it.
        </div>
    </div>
</body>
</html>
