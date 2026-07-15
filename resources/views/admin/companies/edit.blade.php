@extends('layouts.admin')
@section('title', 'Edit ' . $company->name)
@section('content')
<div class="mb-6">
    <a href="/admin/companies/{{ $company->id }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1 mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Company
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Edit Company</h1>
    <p class="text-sm text-gray-500">{{ $company->name }}</p>
</div>
<form method="POST" action="/admin/companies/{{ $company->id }}" class="max-w-xl">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" required maxlength="255"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}" required maxlength="255"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" maxlength="50"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
            <input type="text" name="website" value="{{ old('website', $company->website) }}" maxlength="255"
                placeholder="https://example.com"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('website')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="3" maxlength="500"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('address', $company->address) }}</textarea>
            @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Update Company</button>
        <a href="/admin/companies/{{ $company->id }}" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
