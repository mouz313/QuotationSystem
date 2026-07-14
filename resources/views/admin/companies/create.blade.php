@extends('layouts.admin')
@section('title', 'Create Company')
@section('content')
<div class="mb-6">
    <a href="/admin/companies" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Companies
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Create Company</h1>
    <p class="text-sm text-gray-500">Add a new company with an admin user</p>
</div>
<form method="POST" action="/admin/companies" class="max-w-xl">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Company Details</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                placeholder="Acme Corp" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                placeholder="info@acmecorp.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" maxlength="50"
                placeholder="+1 234 567 890" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="3" maxlength="500"
                placeholder="123 Main St, City, Country" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('address') }}</textarea>
            @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <hr class="my-2">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Admin User</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Name *</label>
            <input type="text" name="admin_name" value="{{ old('admin_name') }}" required maxlength="255"
                placeholder="John Doe" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('admin_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email *</label>
            <input type="email" name="admin_email" value="{{ old('admin_email') }}" required maxlength="255"
                placeholder="admin@acmecorp.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('admin_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
            <input type="password" name="admin_password" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('admin_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
            <input type="password" name="admin_password_confirmation" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Create Company</button>
        <a href="/admin/companies" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
