@extends('layouts.app')
@section('title', 'Order History')

@section('content')
<div style="max-width:48rem;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--surface-900);">Order History</h1>
    </div>

    @if($orders->count() > 0)
    <div style="display:flex;flex-direction:column;gap:.75rem;">
        @foreach($orders as $order)
        <div style="padding:1.25rem;background:var(--surface-0);border:1px solid var(--surface-200);border-radius:.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-size:.875rem;font-weight:700;color:var(--surface-800);">{{ $order->package->name ?? 'N/A' }}</p>
                    <p style="font-size:.75rem;color:var(--surface-400);margin-top:.125rem;">Order #{{ $order->id }} &middot; {{ $order->created_at->format('d M Y g:i A') }}</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:1rem;font-weight:800;color:var(--surface-900);">{{ $order->currency_symbol }}{{ number_format($order->amount, 2) }}</p>
                    @php
                        $badgeStyle = match($order->status) {
                            'paid', 'approved' => 'background:var(--success-100);color:var(--success-700)',
                            'failed', 'rejected' => 'background:var(--danger-100);color:var(--danger-700)',
                            'refunded' => 'background:var(--warning-100);color:var(--warning-700)',
                            default => 'background:var(--surface-100);color:var(--surface-600)',
                        };
                    @endphp
                    <span style="padding:.125rem .5rem;font-size:.625rem;font-weight:700;border-radius:9999px;text-transform:uppercase;{{ $badgeStyle }}">{{ $order->status }}</span>
                </div>
            </div>
            @if($order->payment_method)
            <p style="font-size:.75rem;color:var(--surface-400);margin-top:.5rem;">Method: {{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</p>
            @endif
            @if($order->notes)
            <p style="font-size:.75rem;color:var(--surface-500);margin-top:.25rem;">{{ $order->notes }}</p>
            @endif
        </div>
        @endforeach
    </div>
    {{ $orders->links() }}
    @else
    <div style="padding:3rem;text-align:center;background:var(--surface-0);border:1px solid var(--surface-200);border-radius:1rem;">
        <p style="font-size:.875rem;color:var(--surface-400);">No orders yet.</p>
    </div>
    @endif
</div>
@endsection
