<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - QuotationSystem</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-white min-h-screen fixed flex flex-col">
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
            <a href="/settings/profile" class="block text-xs text-gray-400 hover:text-white mb-2">Profile Settings</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full text-left text-sm text-gray-400 hover:text-white">Logout</button>
            </form>
        </div>
    </aside>
    <div class="ml-64 flex-1 p-6">
        <x-alert type="success" />
        <x-alert type="error" />
        @yield('content')
    </div>
</body>
</html>
