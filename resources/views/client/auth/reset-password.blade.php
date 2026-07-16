@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 w-full max-w-md mx-auto">
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800">Reset Password</h1>
        <p class="text-sm text-gray-400 mt-1">Enter your new password below</p>
    </div>

    <form method="POST" action="/client/reset-password" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">New Password</label>
            <input type="password" name="password" required autofocus
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('password') border-red-400 @enderror">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
        </div>
        <button class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">Reset Password</button>
        <div class="text-center">
            <a href="/client/login" class="text-indigo-600 text-sm font-medium hover:underline">Back to login</a>
        </div>
    </form>
</div>
@endsection
