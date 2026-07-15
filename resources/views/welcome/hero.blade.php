<section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50"></div>
    <div class="blob-1 absolute top-0 right-0 w-96 h-96 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/3"></div>
    <div class="blob-2 absolute bottom-0 left-0 w-80 h-80 bg-purple-100 rounded-full blur-3xl opacity-40 translate-y-1/2 -translate-x-1/3"></div>

    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <div class="hero-badge inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium mb-6">
            <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
            SaaS Quotation Management
        </div>

        <h1 class="hero-title text-4xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
            Create Professional<br>
            <span class="text-indigo-600">Quotations</span> in Minutes
        </h1>

        <p class="hero-sub text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            Manage clients, items, and quotations from one powerful dashboard.
            Multi-tenant, multi-currency, and built for teams of any size.
        </p>

        <div class="hero-cta flex flex-col sm:flex-row items-center justify-center gap-4">
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:shadow-xl hover:shadow-indigo-300 hover:-translate-y-0.5">
                    Start Free Trial
                </a>
            @endif
            <a href="#features" class="w-full sm:w-auto px-8 py-3.5 text-base font-semibold text-gray-700 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 hover:text-indigo-600 transition-all hover:-translate-y-0.5">
                See Features
            </a>
        </div>

        <div class="hero-stats mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
            <div>
                <div class="text-3xl font-bold text-gray-900"><span class="counter" data-count="4" data-prefix="">0</span></div>
                <div class="text-sm text-gray-500 mt-1">Active Companies</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900"><span class="counter" data-count="5" data-suffix="+">0</span></div>
                <div class="text-sm text-gray-500 mt-1">Quotations Created</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900"><span class="counter" data-count="8" data-prefix="">0</span></div>
                <div class="text-sm text-gray-500 mt-1">Currencies</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900"><span class="counter" data-count="99.9" data-suffix="%">0</span></div>
                <div class="text-sm text-gray-500 mt-1">Uptime</div>
            </div>
        </div>
    </div>
</section>
