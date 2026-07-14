@extends('layouts.app')
@section('title', 'Add Team User')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-800">Add Team User</h1></div>
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/company/users" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
            <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="staff">Staff</option>
                <option value="company_admin">Company Admin</option>
            </select>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Add User</button>
            <a href="/company/users" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
