@extends('layouts.app')
@section('title', 'Subscription')

@section('content')
<div style="max-width:40rem;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--surface-900);">My Subscription</h1>
    </div>

    @if($currentPackage)
    <div style="background:var(--surface-0);border:1px solid var(--surface-200);border-radius:1rem;padding:1.75rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h2 style="font-size:1.125rem;font-weight:800;color:var(--surface-900);">{{ $currentPackage->package->name }}</h2>
            <span style="padding:.25rem .75rem;background:var(--success-100);color:var(--success-700);font-size:.75rem;font-weight:700;border-radius:9999px;">Active</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:.8125rem;">
            <div><span style="color:var(--surface-400);">Started:</span> <strong>{{ $currentPackage->start_date->format('d M Y') }}</strong></div>
            <div><span style="color:var(--surface-400);">Expires:</span> <strong>{{ $currentPackage->end_date->format('d M Y') }}</strong></div>
            <div><span style="color:var(--surface-400);">Price:</span> <strong>{{ $currentPackage->package->currency_symbol }}{{ number_format($currentPackage->package->price, 2) }}</strong></div>
            <div><span style="color:var(--surface-400);">Days Left:</span> <strong>{{ max(0, \Carbon\Carbon::now()->diffInDays($currentPackage->end_date, false)) }}</strong></div>
        </div>
    </div>
    @else
    <div style="padding:2rem;text-align:center;background:var(--warning-50);border:1px solid var(--warning-100);border-radius:1rem;margin-bottom:1.5rem;">
        <p style="font-size:.875rem;font-weight:700;color:var(--warning-800);">No Active Package</p>
        <p style="font-size:.8125rem;color:var(--warning-600);margin-top:.25rem;">Contact admin or browse available packages.</p>
        <a href="/packages" style="display:inline-block;margin-top:.75rem;padding:.5rem 1rem;background:var(--brand-600);color:white;font-size:.8125rem;font-weight:600;border-radius:.5rem;text-decoration:none;">Browse Packages</a>
    </div>
    @endif

    @if($recentOrders->count() > 0)
    <h3 style="font-size:.875rem;font-weight:700;color:var(--surface-800);margin-bottom:.75rem;">Recent Orders</h3>
    <div style="display:flex;flex-direction:column;gap:.5rem;">
        @foreach($recentOrders as $order)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--surface-0);border:1px solid var(--surface-200);border-radius:.75rem;">
            <div>
                <p style="font-size:.8125rem;font-weight:600;color:var(--surface-800);">{{ $order->package->name ?? 'N/A' }}</p>
                <p style="font-size:.6875rem;color:var(--surface-400);">{{ $order->created_at->format('d M Y g:i A') }}</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">{{ $order->currency_symbol }}{{ number_format($order->amount, 2) }}</p>
                @php
                    $badgeStyle = match($order->status) {
                        'paid', 'approved' => 'background:var(--success-100);color:var(--success-700)',
                        'failed', 'rejected' => 'background:var(--danger-100);color:var(--danger-700)',
                        'refunded' => 'background:var(--warning-100);color:var(--warning-700)',
                        default => 'background:var(--surface-100);color:var(--surface-600)',
                    };
                @endphp
                <span style="padding:.125rem .5rem;font-size:.5625rem;font-weight:700;border-radius:9999px;text-transform:uppercase;{{ $badgeStyle }}">{{ $order->status }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <a href="/packages" style="display:inline-block;margin-top:1rem;padding:.5rem 1rem;font-size:.8125rem;font-weight:600;color:var(--brand-600);text-decoration:none;">Browse All Packages &rarr;</a>
</div>
@endsection
