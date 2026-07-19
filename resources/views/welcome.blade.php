<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'QuotationSystem') }} - Professional Quotation Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <script defer src="/js/app.js?v=2"></script>
    <style>
        :root {
            --brand-50: oklch(0.97 0.02 280);
            --brand-100: oklch(0.93 0.04 280);
            --brand-200: oklch(0.87 0.07 280);
            --brand-300: oklch(0.78 0.10 280);
            --brand-400: oklch(0.68 0.14 280);
            --brand-500: oklch(0.55 0.17 275);
            --brand-600: oklch(0.48 0.19 275);
            --brand-700: oklch(0.40 0.18 275);
            --brand-800: oklch(0.32 0.14 275);
            --brand-900: oklch(0.22 0.10 275);
            --brand-950: oklch(0.15 0.06 275);
            --surface-0: oklch(1.0 0 0);
            --surface-50: oklch(0.98 0.003 260);
            --surface-100: oklch(0.96 0.005 260);
            --surface-200: oklch(0.92 0.008 260);
            --surface-300: oklch(0.87 0.010 260);
            --surface-400: oklch(0.75 0.010 260);
            --surface-500: oklch(0.60 0.010 260);
            --surface-600: oklch(0.48 0.012 260);
            --surface-700: oklch(0.38 0.012 260);
            --surface-800: oklch(0.28 0.012 260);
            --surface-900: oklch(0.20 0.012 260);
            --surface-950: oklch(0.14 0.010 260);
            --success-50: oklch(0.97 0.02 150);
            --success-500: oklch(0.62 0.17 150);
            --success-600: oklch(0.54 0.18 150);
            --info-50: oklch(0.97 0.02 240);
            --info-500: oklch(0.58 0.16 240);
            --warning-50: oklch(0.97 0.03 80);
            --warning-500: oklch(0.70 0.16 80);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        body { font-family: var(--font-sans); background: var(--surface-0); color: var(--surface-900); margin: 0; }

        [data-animate] { opacity: 0; transition: opacity .7s ease, transform .7s ease; }
        [data-animate="fade-up"] { transform: translateY(40px); }
        [data-animate="fade-in"] { transform: none; }
        [data-animate].is-visible { opacity: 1; transform: translateY(0) translateX(0); }
        [data-animate][data-delay].is-visible { transition-delay: var(--d); }

        .hero-badge   { animation: heroFadeUp .6s .1s ease both; }
        .hero-title   { animation: heroFadeUp .7s .25s ease both; }
        .hero-sub     { animation: heroFadeUp .7s .4s ease both; }
        .hero-cta     { animation: heroFadeUp .7s .55s ease both; }
        .hero-stats   { animation: heroFadeUp .7s .7s ease both; }
        @keyframes heroFadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .blob-1 { animation: blobFloat1 8s ease-in-out infinite; }
        .blob-2 { animation: blobFloat2 10s ease-in-out infinite; }
        @keyframes blobFloat1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,-20px) scale(1.05); } }
        @keyframes blobFloat2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-25px,15px) scale(1.08); } }

        .tilt-card { transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease; }
        .tilt-card:hover { transform: translateY(-6px) scale(1.015); box-shadow: 0 20px 40px -12px oklch(0.48 0.19 275 / .15); }

        .step-ring { animation: stepPulse 2.5s ease-in-out infinite; }
        @keyframes stepPulse { 0%,100% { box-shadow: 0 0 0 0 oklch(0.48 0.19 275 / .3); } 50% { box-shadow: 0 0 0 12px oklch(0.48 0.19 275 / 0); } }

        .popular-glow { animation: glowPulse 3s ease-in-out infinite; }
        @keyframes glowPulse { 0%,100% { box-shadow: 0 10px 40px -10px oklch(0.48 0.19 275 / .25); } 50% { box-shadow: 0 10px 50px -5px oklch(0.48 0.19 275 / .4); } }

        .nav-solid { background: oklch(1.0 0 0 / .95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--surface-200); }
        .nav-solid .nav-link { color: var(--surface-700); }
        .nav-solid .nav-link:hover { color: var(--brand-600); }
        .nav-solid .nav-brand { color: var(--brand-600); }

        .pkg-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        @media (max-width: 1024px) { .pkg-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 520px) { .pkg-grid { grid-template-columns: 1fr; } }

        .counter { display: inline-block; }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav id="landing-nav" class="absolute top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="nav-brand text-xl font-bold" style="color:var(--brand-600);transition:color .3s;">QS</a>
            <div class="hidden md:flex items-center gap-8 text-sm">
                <a href="#features" class="nav-link" style="color:var(--surface-600);transition:color .2s;">Features</a>
                <a href="#how-it-works" class="nav-link" style="color:var(--surface-600);transition:color .2s;">How It Works</a>
                <a href="#pricing" class="nav-link" style="color:var(--surface-600);transition:color .2s;">Pricing</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="/dashboard" class="nav-link px-4 py-2 text-sm font-semibold" style="color:var(--brand-600);">Dashboard</a>
                @else
                    <a href="/login" class="nav-link px-4 py-2 text-sm font-medium" style="color:var(--surface-600);">Log in</a>
                    <a href="/register" class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition-all hover:shadow-lg" style="background:var(--brand-600);">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="absolute inset-0" style="background:linear-gradient(135deg, var(--brand-50) 0%, var(--surface-0) 50%, oklch(0.97 0.02 300) 100%);"></div>
        <div class="blob-1 absolute top-0 right-0 w-96 h-96 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/3" style="background:var(--brand-100);"></div>
        <div class="blob-2 absolute bottom-0 left-0 w-80 h-80 rounded-full blur-3xl opacity-40 translate-y-1/2 -translate-x-1/3" style="background:oklch(0.93 0.04 300);"></div>

        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <div class="hero-badge inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium mb-6" style="background:var(--brand-100);color:var(--brand-700);">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background:var(--brand-500);"></span>
                SaaS Quotation Management
            </div>

            <h1 class="hero-title text-4xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6" style="color:var(--surface-900);">
                Create Professional<br>
                <span style="color:var(--brand-600);">Quotations</span> in Minutes
            </h1>

            <p class="hero-sub text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed" style="color:var(--surface-500);">
                Manage clients, items, and quotations from one powerful dashboard.
                Multi-tenant, multi-currency, and built for teams of any size.
            </p>

            <div class="hero-cta flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/register" class="w-full sm:w-auto px-8 py-3.5 text-base font-semibold text-white rounded-xl transition-all hover:shadow-xl hover:-translate-y-0.5" style="background:var(--brand-600);box-shadow:0 4px 14px oklch(0.48 0.19 275 / .25);">
                    Start Free Trial
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-3.5 text-base font-semibold rounded-xl transition-all hover:-translate-y-0.5" style="color:var(--surface-700);background:var(--surface-0);border:1px solid var(--surface-200);">
                    See Features
                </a>
            </div>

            <div class="hero-stats mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
                <div>
                    <div class="text-3xl font-bold" style="color:var(--surface-900);"><span class="counter" data-count="4">0</span></div>
                    <div class="text-sm mt-1" style="color:var(--surface-500);">Active Companies</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color:var(--surface-900);"><span class="counter" data-count="5" data-suffix="+">0</span></div>
                    <div class="text-sm mt-1" style="color:var(--surface-500);">Quotations Created</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color:var(--surface-900);"><span class="counter" data-count="8">0</span></div>
                    <div class="text-sm mt-1" style="color:var(--surface-500);">Currencies</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color:var(--surface-900);"><span class="counter" data-count="99.9" data-suffix="%">0</span></div>
                    <div class="text-sm mt-1" style="color:var(--surface-500);">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-20" style="background:var(--surface-50);">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-animate="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color:var(--surface-900);">Everything You Need</h2>
                <p class="text-lg max-w-2xl mx-auto" style="color:var(--surface-500);">Powerful tools to manage your entire quotation workflow from start to finish.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>', 'title' => 'Multi-Company', 'desc' => 'Manage multiple companies from one platform. Each team gets their own workspace.'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'title' => 'Multi-Currency', 'desc' => 'Work with clients across the globe. Support for 8+ currencies with live rates.'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'title' => 'Professional PDFs', 'desc' => 'Generate branded PDF quotations with your logo, colors, and payment details.'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>', 'title' => 'Client Portal', 'desc' => 'Dedicated portal where clients can view quotes, accept/decline, and submit payments.'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>', 'title' => 'Analytics & Reports', 'desc' => 'Track revenue, conversion rates, and quotation pipeline with interactive charts.'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>', 'title' => 'Real-Time Notifications', 'desc' => 'Get notified instantly when clients view quotes, submit payments, or change status.'],
                    ];
                @endphp
                @foreach($features as $i => $f)
                    <div class="tilt-card p-6 rounded-xl transition-all duration-300" style="background:var(--surface-0);border:1px solid var(--surface-200);" data-animate="fade-up" data-delay style="--d:{{ $i * 0.08 }}s">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background:var(--brand-50);color:var(--brand-600);">
                            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                        </div>
                        <h3 class="text-base font-bold mb-2" style="color:var(--surface-900);">{{ $f['title'] }}</h3>
                        <p class="text-sm leading-relaxed" style="color:var(--surface-500);">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section id="how-it-works" class="py-20" style="background:var(--surface-0);">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-animate="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color:var(--surface-900);">How It Works</h2>
                <p class="text-lg max-w-2xl mx-auto" style="color:var(--surface-500);">Get up and running in three simple steps.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                @php
                    $steps = [
                        ['num' => '1', 'title' => 'Set Up Your Company', 'desc' => 'Register your account and configure your company profile with branding details.'],
                        ['num' => '2', 'title' => 'Add Clients & Items', 'desc' => 'Create your client database and catalog of services or products with pricing.'],
                        ['num' => '3', 'title' => 'Create & Send', 'desc' => 'Build professional quotations and send them directly to your clients via email.'],
                    ];
                @endphp
                @foreach($steps as $i => $s)
                    <div class="text-center" data-animate="fade-up" data-delay style="--d:{{ $i * 0.1 }}s">
                        <div class="step-ring w-14 h-14 rounded-full flex items-center justify-center text-lg font-bold text-white mx-auto mb-4" style="background:var(--brand-600);">{{ $s['num'] }}</div>
                        <h3 class="text-base font-bold mb-2" style="color:var(--surface-900);">{{ $s['title'] }}</h3>
                        <p class="text-sm" style="color:var(--surface-500);">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="py-20" style="background:var(--surface-50);">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-animate="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color:var(--surface-900);">Simple, Transparent Pricing</h2>
                <p class="text-lg max-w-2xl mx-auto" style="color:var(--surface-500);">Choose the plan that fits your team. All plans include core features.</p>
            </div>

            <div class="pkg-grid max-w-6xl mx-auto">
                @forelse($packages as $index => $package)
                    @php
                        $isFree = $package->price == 0;
                        $isPopular = $index === 2;
                    @endphp
                    <div class="tilt-card relative p-6 rounded-xl transition-all duration-300 {{ $isPopular ? 'popular-glow scale-[1.03]' : '' }}" style="background:var(--surface-0);border:{{ $isPopular ? '2px solid var(--brand-600)' : '1px solid var(--surface-200)' }};" data-animate="fade-up" data-delay style="--d:{{ $index * 0.08 }}s">
                        @if($isPopular)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 text-xs font-bold rounded-full text-white" style="background:var(--brand-600);">Most Popular</div>
                        @endif
                        @if($isFree)
                            <div class="absolute -top-3 right-4 px-3 py-1 text-xs font-bold rounded-full" style="background:var(--success-50);color:var(--success-600);">Free</div>
                        @endif

                        <h3 class="text-base font-bold mb-1" style="color:var(--surface-900);">{{ $package->name }}</h3>
                        <p class="text-sm mb-4" style="color:var(--surface-500);">{{ $package->description }}</p>

                        <div class="mb-5">
                            @if($isFree)
                                <span class="text-3xl font-extrabold" style="color:var(--surface-900);">Free</span>
                            @else
                                <span class="text-3xl font-extrabold" style="color:var(--surface-900);">${{ number_format($package->price, 2) }}</span>
                                <span class="text-sm" style="color:var(--surface-400);">/ {{ $package->duration_days }}d</span>
                            @endif
                        </div>

                        <ul class="space-y-2.5 mb-6">
                            <li class="flex items-center gap-2.5 text-sm" style="color:var(--surface-600);">
                                <svg style="width:1rem;height:1rem;flex-shrink:0;color:var(--brand-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $package->max_users }} {{ Str::plural('Team User', $package->max_users) }}
                            </li>
                            <li class="flex items-center gap-2.5 text-sm" style="color:var(--surface-600);">
                                <svg style="width:1rem;height:1rem;flex-shrink:0;color:var(--brand-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $package->max_clients }} {{ Str::plural('Client', $package->max_clients) }}
                            </li>
                            <li class="flex items-center gap-2.5 text-sm" style="color:var(--surface-600);">
                                <svg style="width:1rem;height:1rem;flex-shrink:0;color:var(--brand-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ number_format($package->max_quotations) }} {{ Str::plural('Quotation', $package->max_quotations) }}
                            </li>
                            <li class="flex items-center gap-2.5 text-sm" style="color:var(--surface-600);">
                                <svg style="width:1rem;height:1rem;flex-shrink:0;color:var(--brand-500);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                PDF export &amp; reports
                            </li>
                        </ul>

                        <a href="/register" class="block w-full py-2.5 text-center text-sm font-semibold rounded-lg transition-all duration-300" style="{{ $isPopular ? 'background:var(--brand-600);color:white;' : 'background:var(--surface-100);color:var(--surface-700);' }}">
                            Get Started
                        </a>
                    </div>
                @empty
                    <div class="text-center py-12" style="color:var(--surface-400);grid-column:1/-1;">
                        <p>Packages coming soon. Contact us for details.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-12" style="background:var(--surface-900);">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-xl font-bold" style="color:var(--surface-0);">QS</div>
                <div class="flex items-center gap-6 text-sm" style="color:var(--surface-400);">
                    <a href="/pages/about" class="hover:text-white transition-colors">About</a>
                    <a href="/pages/terms" class="hover:text-white transition-colors">Terms</a>
                    <a href="/pages/privacy" class="hover:text-white transition-colors">Privacy</a>
                </div>
                <div class="text-sm" style="color:var(--surface-500);">&copy; {{ date('Y') }} QuotationSystem. All rights reserved.</div>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var animated = document.querySelectorAll('[data-animate]');
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            animated.forEach(function(el) { observer.observe(el); });
        } else {
            animated.forEach(function(el) { el.classList.add('is-visible'); });
        }

        var nav = document.getElementById('landing-nav');
        if (nav) {
            window.addEventListener('scroll', function() {
                nav.classList.toggle('nav-solid', window.scrollY > 50);
            });
        }

        var counters = document.querySelectorAll('[data-count]');
        if (counters.length && 'IntersectionObserver' in window) {
            var cObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        cObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(function(c) { cObserver.observe(c); });
        }

        function animateCounter(el) {
            var target = el.getAttribute('data-count');
            var suffix = el.getAttribute('data-suffix') || '';
            var prefix = el.getAttribute('data-prefix') || '';
            var isDecimal = target.indexOf('.') !== -1;
            var endVal = parseFloat(target);
            var duration = 1800;
            var start = performance.now();
            function step(now) {
                var progress = Math.min((now - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var current = eased * endVal;
                el.textContent = prefix + (isDecimal ? current.toFixed(1) : Math.floor(current)) + suffix;
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }
    });
    </script>
</body>
</html>
