<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - QuotationSystem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex" x-data="{ sidebarOpen: false }">
    <!-- Mobile top bar -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-gray-900 text-white h-14 flex items-center px-4 gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        @php $companyLogo = auth()->user()->company?->logo_url; @endphp
        @if($companyLogo)
            <img src="{{ $companyLogo }}" alt="Logo" class="w-7 h-7 rounded-lg object-cover">
        @endif
        <span class="text-sm font-bold text-indigo-400">{{ auth()->user()->company?->name ?? 'QuotationSystem' }}</span>
        @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
        <div class="relative ml-auto">
            <button onclick="document.getElementById('notifDropdown').classList.toggle('hidden')" class="relative p-2 text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>
            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border z-50 max-h-96 overflow-y-auto">
                <div class="p-3 border-b flex justify-between items-center">
                    <span class="font-semibold text-sm text-gray-800">Notifications</span>
                    @if($unreadCount > 0)
                        <form method="POST" action="/company/notifications/mark-all-read" class="inline">
                            @csrf
                            <button class="text-xs text-indigo-600 hover:underline">Mark all read</button>
                        </form>
                    @endif
                </div>
                @php $notifs = auth()->user()->notifications()->latest()->limit(20)->get(); @endphp
                @forelse($notifs as $notif)
                    <a href="{{ $notif->url ?? '#' }}" class="block px-3 py-2 border-b last:border-0 hover:bg-gray-50 {{ $notif->is_read ? 'opacity-60' : '' }}">
                        <p class="text-xs text-gray-700">{{ $notif->message }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <div class="p-4 text-center text-gray-400 text-xs">No notifications yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 z-40 bg-black/50" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="lg:translate-x-0 fixed z-50 lg:z-auto w-64 bg-gray-900 text-white min-h-screen flex flex-col transition-transform duration-300 ease-in-out">
        <div class="p-4 border-b border-gray-800 flex items-center gap-3">
            @php $companyLogo = auth()->user()->company?->logo_url; @endphp
            @if($companyLogo)
                <img src="{{ $companyLogo }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover">
            @endif
            <a href="/dashboard" class="text-lg font-bold text-indigo-400">{{ auth()->user()->company?->name ?? 'QuotationSystem' }}</a>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <x-nav-link href="/dashboard" icon="dashboard">Dashboard</x-nav-link>
            @if(auth()->user()->isCompanyAdmin())
                <x-nav-link href="/company/users" active="company/users*" icon="users">Team Users</x-nav-link>
                <x-nav-link href="/company/settings" active="company/settings*" icon="settings">Company Settings</x-nav-link>
            @endif
            <x-nav-link href="/clients" active="clients*" icon="client">Clients</x-nav-link>
            <x-nav-link href="/items" active="items*" icon="item">Items</x-nav-link>
            <x-nav-link href="/quotations" active="quotations*" icon="quote">Quotations</x-nav-link>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <div class="text-xs text-gray-500 mb-2">{{ auth()->user()->name }}</div>
            <div class="text-xs text-gray-600 mb-2">{{ auth()->user()->role }}</div>
            @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
            <div class="relative mb-2">
                <button onclick="document.getElementById('notifDropdownSidebar').classList.toggle('hidden')" class="relative flex items-center gap-2 text-xs text-gray-400 hover:text-white w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifications
                    @if($unreadCount > 0)
                        <span class="ml-auto w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
            </div>
            <a href="/settings/profile" class="block text-xs text-gray-400 hover:text-white mb-2">Profile Settings</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full text-left text-sm text-gray-400 hover:text-white">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <div class="lg:ml-64 flex-1 pt-14 lg:pt-0 p-6">
        <x-alert type="success" />
        <x-alert type="error" />
        @yield('content')
    </div>
</body>
</html>
