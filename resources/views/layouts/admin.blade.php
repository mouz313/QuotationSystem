<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - QuotationSystem</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }">

    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" :class="sidebarOpen ? 'visible' : ''" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="dash-sidebar" :class="sidebarOpen ? 'open' : ''">
        <a href="/admin/dashboard" class="sidebar-brand">
            <span class="brand-icon">QS</span>
            <span>QS Admin</span>
        </a>

        <nav class="sidebar-nav">
            <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Management</div>
            @if(in_array('companies.manage', $userPermissions))
            <a href="/admin/companies" class="sidebar-link {{ request()->is('admin/companies*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Companies
            </a>
            @endif
            @if(in_array('packages.manage', $userPermissions))
            <a href="/admin/packages" class="sidebar-link {{ request()->is('admin/packages*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Packages
            </a>
            @endif
            @if(in_array('currencies.manage', $userPermissions))
            <a href="/admin/currencies" class="sidebar-link {{ request()->is('admin/currencies*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Currencies
            </a>
            @endif
            @if(in_array('taxes.manage', $userPermissions))
            <a href="/admin/taxes" class="sidebar-link {{ request()->is('admin/taxes*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Taxes
            </a>
            @endif

            <div class="sidebar-section-label">Oversight</div>
            @if(in_array('quotations.view', $userPermissions))
            <a href="/admin/quotations" class="sidebar-link {{ request()->is('admin/quotations*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Quotations
            </a>
            @endif
            @if(in_array('users.manage', $userPermissions))
            <a href="/admin/users" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Admin Users
            </a>
            <a href="/admin/roles" class="sidebar-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Roles & Permissions
            </a>
            @endif
            @if(in_array('companies.manage', $userPermissions))
            <a href="/admin/company-users" class="sidebar-link {{ request()->is('admin/company-users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Company Users
            </a>
            <a href="/admin/client-users" class="sidebar-link {{ request()->is('admin/client-users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Client Users
            </a>
            @endif

            <div class="sidebar-section-label">Reports & Tools</div>
            @if(in_array('reports.view', $userPermissions))
            <a href="/admin/reports" class="sidebar-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports & Exports
            </a>
            @endif
            @if(in_array('activity.view', $userPermissions))
            <a href="/admin/activity-log" class="sidebar-link {{ request()->is('admin/activity-log*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Activity Log
            </a>
            @endif
            @if(in_array('health.view', $userPermissions))
            <a href="/admin/health" class="sidebar-link {{ request()->is('admin/health*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                System Health
            </a>
            @endif

            <div class="sidebar-section-label">Content</div>
            @if(in_array('pages.manage', $userPermissions))
            <a href="/admin/pages" class="sidebar-link {{ request()->is('admin/pages*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Pages
            </a>
            @endif
            @if(in_array('email_templates.manage', $userPermissions))
            <a href="/admin/email-templates" class="sidebar-link {{ request()->is('admin/email-templates*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Email Templates
            </a>
            @endif

            <div class="sidebar-section-label">System</div>
            @if(in_array('settings.manage', $userPermissions))
            <a href="/admin/settings" class="sidebar-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
            @endif
            <a href="/settings/profile" class="sidebar-link {{ request()->is('settings/profile*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>
        </nav>

        <div class="sidebar-footer">
            @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
            <div class="sidebar-user-chip">
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Super Admin</div>
                </div>
                <form method="POST" action="/logout" class="flex-shrink-0">
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
                    <h1 class="text-sm font-bold text-surface-800">@yield('header-title', 'Dashboard')</h1>
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
                                <form method="POST" action="/notifications/mark-all-read" class="inline">
                                    @csrf
                                    <button class="text-xs font-semibold" style="color:var(--brand-600);">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        @php $notifs = auth()->user()->notifications()->latest()->limit(15)->get(); @endphp
                        @forelse($notifs as $notif)
                            <form method="POST" action="/notifications/{{ $notif->id }}/read" class="block">
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
                            <a href="/admin/notifications" class="notif-footer">View All Notifications</a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <div class="dash-content">
            <x-alert type="success" />
            <x-alert type="error" />
            @yield('content')
        </div>
    </div>
</body>
</html>
