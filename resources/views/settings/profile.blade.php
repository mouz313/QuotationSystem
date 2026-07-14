@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Profile Settings')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Profile Settings</h1>
    <p class="text-sm text-gray-500">Manage your personal account information</p>
</div>

@php
    $tabs = [
        'profile'  => 'Profile',
        'details'  => 'Account Details',
        'password' => 'Change Password',
    ];
    $active = request('tab', 'profile');
@endphp

<div class="bg-white rounded-xl shadow overflow-hidden">
    {{-- Tab Headers --}}
    <div class="flex border-b">
        @foreach($tabs as $key => $label)
            <a href="?tab={{ $key }}"
               class="px-6 py-3 text-sm font-medium transition {{ $active === $key ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="p-6">

        {{-- ═══════════════ PROFILE TAB ═══════════════ --}}
        @if($active === 'profile')
            <form method="POST" action="/settings/profile" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Save Changes</button>
            </form>

        {{-- ═══════════════ ACCOUNT DETAILS TAB ═══════════════ --}}
        @elseif($active === 'details')
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Role:</span>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $user->role }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Member since:</span>
                    <span class="ml-2">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                @if($user->company)
                <div>
                    <span class="text-gray-500">Company:</span>
                    <span class="ml-2">{{ $user->company->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Company Status:</span>
                    <span class="ml-2">
                        @if($user->company->status === 'active')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                        @elseif($user->company->status === 'blocked')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>

        {{-- ═══════════════ CHANGE PASSWORD TAB ═══════════════ --}}
        @elseif($active === 'password')
            <form method="POST" action="/settings/password" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Update Password</button>
            </form>
        @endif

    </div>
</div>
@endsection
