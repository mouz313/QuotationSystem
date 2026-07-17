@extends('layouts.admin')
@section('title', 'Notifications')
@section('header-title', 'Notifications')
@section('header-sub', 'All your activity updates in one place')
@section('content')

<div class="fade-in">
    @if(auth()->user()->notifications()->where('is_read', false)->exists())
        <form method="POST" action="/admin/notifications/mark-all-read" style="margin-bottom:1rem;">
            @csrf
            <button type="submit" class="btn btn-brand btn-sm">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Mark all read
            </button>
        </form>
    @endif

    @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
    @if($unreadCount > 0)
    <div style="margin-bottom:1.25rem;padding:1rem;border-radius:.75rem;background:var(--brand-50);border:1px solid var(--brand-100);display:flex;align-items:center;gap:.75rem;">
        <div style="width:2rem;height:2rem;border-radius:9999px;background:var(--brand-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:1rem;height:1rem;color:var(--brand-600);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <p style="font-size:.8125rem;font-weight:600;color:var(--brand-800);">You have {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</p>
    </div>
    @endif

    <div class="d-card" style="overflow:hidden;">
        @forelse($notifications as $notif)
            @php
                $iconBg = match($notif->type) {
                    'payment_submitted' => 'background:var(--warning-50);color:var(--warning-600)',
                    'payment_approved' => 'background:var(--success-50);color:var(--success-600)',
                    'payment_rejected' => 'background:var(--danger-50);color:var(--danger-600)',
                    'status_changed' => 'background:var(--info-50);color:var(--info-600)',
                    'payment_reminder_sent' => 'background:var(--brand-50);color:var(--brand-600)',
                    default => 'background:var(--surface-100);color:var(--surface-600)',
                };
                $icon = match($notif->type) {
                    'payment_submitted' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'payment_approved' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'payment_rejected' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'status_changed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                    'payment_reminder_sent' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                    default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                };
            @endphp
            <a href="{{ $notif->url ?? '#' }}" style="display:flex;align-items:flex-start;gap:.875rem;padding:1rem 1.25rem;border-bottom:1px solid var(--surface-50);transition:background .15s;{{ !$notif->is_read ? 'background:var(--brand-50);' : '' }}" onmouseover="this.style.background='var(--surface-50)'" onmouseout="this.style.background='{{ !$notif->is_read ? 'var(--brand-50)' : 'transparent' }}'">
                <div style="width:2.25rem;height:2.25rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;flex-shrink:0;{{ $iconBg }}">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:.8125rem;color:var(--surface-800);{{ $notif->is_read ? '' : 'font-weight:600;' }}">{{ $notif->message }}</p>
                    <p style="font-size:.75rem;color:var(--surface-400);margin-top:.25rem;">{{ $notif->created_at->diffForHumans() }} · {{ $notif->created_at->format('d M Y g:i A') }}</p>
                </div>
                @if(!$notif->is_read)
                    <div style="width:.5rem;height:.5rem;border-radius:9999px;background:var(--brand-500);margin-top:.5rem;flex-shrink:0;"></div>
                @endif
            </a>
        @empty
            <x-empty-state icon="info" title="No notifications yet." description="You'll see updates about payments, status changes, and more." />
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div style="margin-top:1.25rem;">
        {{ $notifications->withQueryString()->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
