<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Portal') - {{ auth('client')->user()->companies->first()->name ?? 'Client Portal' }}</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }">

    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" :class="sidebarOpen ? 'visible' : ''" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="dash-sidebar" :class="sidebarOpen ? 'open' : ''">
        @php
            $clientUser = auth('client')->user();
            $primaryCompany = $clientUser->companies->first();
        @endphp
        <a href="/client/dashboard" class="sidebar-brand">
            @if($primaryCompany?->logo_url)
                <img src="{{ $primaryCompany->logo_url }}" alt="Logo">
            @else
                <span class="brand-icon">{{ strtoupper(substr($clientUser->name, 0, 2)) }}</span>
            @endif
            <span>{{ $clientUser->name }}</span>
        </a>

        <nav class="sidebar-nav">
            <a href="/client/dashboard" class="sidebar-link {{ request()->is('client/dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                Dashboard
            </a>
            <a href="/client/quotations" class="sidebar-link {{ request()->is('client/quotations*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Quotations
            </a>
            <a href="/client/payments" class="sidebar-link {{ request()->is('client/payments*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Payments
            </a>
            <a href="/client/profile" class="sidebar-link {{ request()->is('client/profile') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile
            </a>
        </nav>

        <div class="sidebar-footer">
            @php
                $unreadCount = auth('client')->user()->unreadNotificationsCount();
            @endphp
            <a href="/client/notifications" class="sidebar-link mb-1 {{ request()->is('client/notifications*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
                @if($unreadCount > 0)
                    <span class="ml-auto badge badge-sent" style="padding:.1rem .4rem;font-size:.6rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </a>
            <div class="sidebar-user-chip">
                <span class="avatar">{{ strtoupper(substr($clientUser->name, 0, 2)) }}</span>
                <div class="user-info">
                    <div class="user-name">{{ $clientUser->name }}</div>
                    <div class="user-role">Client</div>
                </div>
                <form method="POST" action="/client/logout" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Logout" class="text-surface-500 hover:text-danger-500 transition">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="dash-main">
        <header class="dash-header">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-surface-600 hover:text-surface-800">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1.375rem;height:1.375rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-sm font-bold text-surface-800">@yield('header-title', 'Client Portal')</h1>
                    <p class="text-xs text-surface-400">@yield('header-sub')</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="notif-trigger btn btn-ghost btn-sm" style="padding:.4rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadCount > 0)
                            <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="notif-dropdown">
                        <div style="padding:.75rem 1rem;border-bottom:1px solid var(--surface-100);display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:.8125rem;font-weight:700;color:var(--surface-800);">Notifications</span>
                            @if($unreadCount > 0)
                                <form method="POST" action="/client/notifications/mark-all-read" class="inline">
                                    @csrf
                                    <button class="text-xs font-semibold" style="color:var(--brand-600);">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        @php $notifs = auth('client')->user()->notifications()->latest()->limit(setting_int('notification_limit', 15))->get(); @endphp
                        @forelse($notifs as $notif)
                            <form method="POST" action="/client/notifications/{{ $notif->id }}/read" class="block">
                                @csrf
                                <button type="submit" class="notif-item {{ $notif->is_read ? '' : 'unread' }}">
                                    <div style="font-size:.8125rem;line-height:1.4;">{{ $notif->message }}</div>
                                    <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                                </button>
                            </form>
                        @empty
                            <div class="notif-empty">No notifications yet.</div>
                        @endforelse
                        @if($notifs->count() > 0)
                            <a href="/client/notifications" class="notif-footer">View All Notifications</a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <div class="dash-content">
            @if(session('success'))
                <div style="margin-bottom:1rem;padding:.75rem 1rem;background:var(--success-50);border:1px solid oklch(0.88 0.04 150);border-radius:.625rem;color:var(--success-700);font-size:.8125rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom:1rem;padding:.75rem 1rem;background:var(--danger-50);border:1px solid oklch(0.88 0.04 25);border-radius:.625rem;color:var(--danger-700);font-size:.8125rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</body>
</html>
