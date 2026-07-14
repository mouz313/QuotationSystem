<nav class="absolute top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-bold text-indigo-600">QuotationSystem</a>
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
            <a href="#features" class="hover:text-indigo-600 transition">Features</a>
            <a href="#how-it-works" class="hover:text-indigo-600 transition">How It Works</a>
            <a href="#pricing" class="hover:text-indigo-600 transition">Pricing</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="/dashboard" class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Log in</a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">Get Started</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
