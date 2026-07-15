<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'QuotationSystem') }} - Professional Quotation Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }

        /* ── Scroll animations ── */
        [data-animate] {
            opacity: 0;
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        [data-animate="fade-up"]    { transform: translateY(40px); }
        [data-animate="fade-in"]    { transform: none; }
        [data-animate="slide-left"] { transform: translateX(-60px); }
        [data-animate="slide-right"]{ transform: translateX(60px); }
        [data-animate="scale-up"]   { transform: scale(0.9); }

        [data-animate].is-visible {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }

        /* stagger children */
        [data-animate][data-delay].is-visible { transition-delay: var(--d); }

        /* ── Hero entrance (plays on load, not scroll) ── */
        .hero-badge   { animation: heroFadeUp 0.6s 0.1s ease both; }
        .hero-title   { animation: heroFadeUp 0.7s 0.25s ease both; }
        .hero-sub     { animation: heroFadeUp 0.7s 0.4s ease both; }
        .hero-cta     { animation: heroFadeUp 0.7s 0.55s ease both; }
        .hero-stats   { animation: heroFadeUp 0.7s 0.7s ease both; }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Floating blobs ── */
        .blob-1 { animation: blobFloat1 8s ease-in-out infinite; }
        .blob-2 { animation: blobFloat2 10s ease-in-out infinite; }

        @keyframes blobFloat1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%      { transform: translate(30px, -20px) scale(1.05); }
        }
        @keyframes blobFloat2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%      { transform: translate(-25px, 15px) scale(1.08); }
        }

        /* ── Number counter ── */
        .counter { display: inline-block; }

        /* ── Card tilt on hover ── */
        .tilt-card {
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        }
        .tilt-card:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 20px 40px -12px rgba(99,102,241,0.15);
        }

        /* ── Step pulse ring ── */
        .step-ring {
            animation: stepPulse 2.5s ease-in-out infinite;
        }
        @keyframes stepPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(79,70,229,0.3); }
            50%      { box-shadow: 0 0 0 12px rgba(79,70,229,0); }
        }

        /* ── Pricing popular glow ── */
        .popular-glow {
            animation: glowPulse 3s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 10px 40px -10px rgba(79,70,229,0.25); }
            50%      { box-shadow: 0 10px 50px -5px rgba(79,70,229,0.4); }
        }

        /* ── Navbar scroll transition ── */
        .nav-solid {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-solid .nav-link { color: #374151; }
        .nav-solid .nav-link:hover { color: #4f46e5; }
        .nav-solid .nav-brand { color: #4f46e5; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    @include('welcome.navbar')
    @include('welcome.hero')
    @include('welcome.features')
    @include('welcome.how-it-works')
    @include('welcome.pricing')
    @include('welcome.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ── Scroll-triggered animations ── */
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

        /* ── Navbar solid on scroll ── */
        var nav = document.getElementById('landing-nav');
        if (nav) {
            window.addEventListener('scroll', function() {
                nav.classList.toggle('nav-solid', window.scrollY > 50);
            });
        }

        /* ── Animated counters ── */
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
