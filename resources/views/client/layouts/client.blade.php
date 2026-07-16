<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Portal') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-14">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-base font-bold text-gray-800 hidden sm:block">Client Portal</span>
                </div>
                <div class="flex items-center gap-0.5">
                    <a href="/client/dashboard" class="px-3 py-2 text-sm font-medium {{ request()->is('client/dashboard') ? 'text-indigo-600 bg-indigo-50 rounded-lg' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-lg' }} transition">Dashboard</a>
                    <a href="/client/profile" class="px-3 py-2 text-sm font-medium {{ request()->is('client/profile') ? 'text-indigo-600 bg-indigo-50 rounded-lg' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-lg' }} transition">Profile</a>
                    <div class="w-px h-5 bg-gray-200 mx-1.5"></div>
                    <form method="POST" action="/client/logout" class="inline">
                        @csrf
                        <button class="px-3 py-2 text-sm font-medium text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-5">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
