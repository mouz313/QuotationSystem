@extends('client.layouts.client')
@section('title', 'Profile')
@section('content')

<style>
    @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    .fade-in{animation:fadeUp .35s ease-out both}
</style>

<div class="mb-5 fade-in">
    <h1 class="text-xl font-bold text-gray-800">My Profile</h1>
    <p class="text-sm text-gray-400 mt-1">Manage your account settings</p>
</div>

<div class="max-w-lg space-y-5">
    {{-- Personal Info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.05s">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Personal Information</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="/client/profile" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $clientUser->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $clientUser->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $clientUser->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                </div>
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">Update Profile</button>
            </form>
        </div>
    </div>

    {{-- Password --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden fade-in" style="animation-delay:.1s">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Change Password</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="/client/profile/password" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white @error('password') border-red-400 @enderror">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                </div>
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">Update Password</button>
            </form>
        </div>
    </div>
</div>

@endsection
