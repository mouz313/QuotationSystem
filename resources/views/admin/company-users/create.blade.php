@extends('layouts.admin')
@section('title', 'Create Company User')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Company User</h1>
</div>
<form method="POST" action="/admin/company-users" class="max-w-xl">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
            <select name="company_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Select company...</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
            <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">Select role...</option>
                <option value="company_admin" {{ old('role') == 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
            <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Create User</button>
        <a href="/admin/company-users" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
