<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - QuotationSystem</title>
    @vite(['resources/css/app.css'])
    <style>
        [data-animate] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        [data-animate].is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="min-h-screen flex">
    {{-- Top Nav --}}
    <nav class="absolute top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold text-indigo-600">QuotationSystem</a>
            <div class="flex items-center gap-3">
                <a href="/login" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Log in</a>
                <a href="/login" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all hover:shadow-lg">Sign In</a>
            </div>
        </div>
    </nav>

    {{-- Left: Branding --}}
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-indigo-600 to-indigo-800 items-center justify-center p-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="relative text-center text-white max-w-md" data-animate>
            <div class="text-5xl font-extrabold mb-4">QS</div>
            <h2 class="text-2xl font-bold mb-3">Already Have an Account?</h2>
            <p class="text-indigo-200 text-sm leading-relaxed mb-8">Sign in to access your dashboard, manage quotations, and stay on top of your business.</p>
            <a href="/login" class="inline-block px-8 py-3 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 transition shadow-lg">
                Sign In
            </a>
        </div>
    </div>

    {{-- Right: Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
        <div class="w-full max-w-md" data-animate>
            <a href="/" class="inline-block text-2xl font-bold text-indigo-600 mb-8">QuotationSystem</a>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Create Account</h1>
            <p class="text-gray-500 mb-8">Get started with QuotationSystem</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <button type="submit"
                    class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:shadow-xl hover:-translate-y-0.5">
                    Create Account
                </button>
            </form>
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
