<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - QuotationSystem</title>
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --brand-50: oklch(0.97 0.02 280);
            --brand-100: oklch(0.93 0.04 280);
            --brand-500: oklch(0.55 0.17 275);
            --brand-600: oklch(0.48 0.19 275);
            --brand-700: oklch(0.40 0.18 275);
            --brand-900: oklch(0.22 0.10 275);
            --surface-0: oklch(1.0 0 0);
            --surface-50: oklch(0.98 0.003 260);
            --surface-100: oklch(0.96 0.005 260);
            --surface-200: oklch(0.92 0.008 260);
            --surface-400: oklch(0.75 0.010 260);
            --surface-500: oklch(0.60 0.010 260);
            --surface-600: oklch(0.48 0.012 260);
            --surface-700: oklch(0.38 0.012 260);
            --surface-900: oklch(0.20 0.012 260);
            --danger-50: oklch(0.97 0.02 25);
            --danger-600: oklch(0.52 0.20 25);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-font-smoothing: antialiased; }
        body { font-family: var(--font-sans); margin: 0; }
        .auth-input {
            width: 100%; padding: .625rem .875rem; border: 1px solid var(--surface-200);
            border-radius: .5rem; font-size: .875rem; outline: none; transition: border-color .15s, box-shadow .15s;
            background: var(--surface-0); color: var(--surface-900);
        }
        .auth-input:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px oklch(0.48 0.19 275 / .12); }
        [data-animate] {
            opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease, transform 0.6s ease;
        }
        [data-animate].is-visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="min-h-screen flex">
    {{-- Left: Branding --}}
    <div class="hidden lg:flex w-1/2 items-center justify-center p-16 relative overflow-hidden" style="background:linear-gradient(135deg, var(--brand-600), var(--brand-900));">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative text-center text-white max-w-md" data-animate>
            <div class="text-5xl font-extrabold mb-4">QS</div>
            <h2 class="text-2xl font-bold mb-3">Already Have an Account?</h2>
            <p class="text-sm leading-relaxed mb-8" style="color:oklch(0.87 0.07 280);">Sign in to access your dashboard, manage quotations, and stay on top of your business.</p>
            <a href="/login" class="inline-block px-8 py-3 bg-white font-semibold rounded-xl hover:bg-white/90 transition shadow-lg" style="color:var(--brand-700);">
                Sign In
            </a>
        </div>
    </div>

    {{-- Right: Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16" style="background:var(--surface-0);">
        <div class="w-full max-w-md" data-animate>
            <a href="/" class="inline-block text-2xl font-bold mb-8" style="color:var(--brand-600);">QS</a>
            <h1 class="text-3xl font-bold mb-1" style="color:var(--surface-900);">Create Your Account</h1>
            <p class="mb-8" style="color:var(--surface-500);">Start managing quotations for your business today.</p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg text-sm" style="background:var(--danger-50);border:1px solid oklch(0.93 0.04 25);color:var(--danger-600);">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--surface-700);">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required autofocus
                        placeholder="e.g. Acme Corporation" class="auth-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--surface-700);">Your Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. John Doe" class="auth-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:var(--surface-700);">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="you@company.com" class="auth-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--surface-700);">Password</label>
                        <input type="password" name="password" required class="auth-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:var(--surface-700);">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="auth-input">
                    </div>
                </div>
                <div class="flex items-start gap-3 pt-1">
                    <input type="checkbox" name="terms" id="terms" required
                        class="mt-0.5 w-4 h-4 rounded" style="accent-color:var(--brand-600);">
                    <label for="terms" class="text-sm" style="color:var(--surface-600);">
                        I agree to the <a href="/pages/terms" target="_blank" style="color:var(--brand-600);">Terms &amp; Conditions</a>
                        and <a href="/pages/privacy" target="_blank" style="color:var(--brand-600);">Privacy Policy</a>
                    </label>
                </div>
                <button type="submit" class="w-full py-2.5 text-white font-semibold rounded-lg transition-all hover:shadow-lg" style="background:var(--brand-600);border:none;cursor:pointer;margin-top:0.5rem;">
                    Start Free Trial
                </button>
            </form>

            <p class="mt-6 text-center text-sm" style="color:var(--surface-500);">
                Free 30-day trial — no credit card required.
            </p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-animate]').forEach(function(el, i) {
            setTimeout(function() { el.classList.add('is-visible'); }, i * 150);
        });
    });
    </script>
</body>
</html>
