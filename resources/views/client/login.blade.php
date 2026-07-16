@extends('layouts.guest')
@section('title', 'Client Login')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 w-full max-w-md mx-auto">
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800">Client Portal</h1>
        <p class="text-sm text-gray-400 mt-1">Sign in to view your quotations</p>
    </div>

    <form method="POST" action="/client/login" class="space-y-4">
        @csrf
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('email') border-red-400 @enderror">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Password</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('password') border-red-400 @enderror">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-gray-500"><input type="checkbox" name="remember" class="rounded border-gray-300"> Remember me</label>
            <a href="/client/forgot-password" class="text-indigo-600 text-xs font-medium hover:underline">Forgot password?</a>
        </div>
        <button class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">Sign In</button>
    </form>
</div>
@endsection
