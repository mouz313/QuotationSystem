@extends('layouts.admin')
@section('title', 'Edit Package')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Package: {{ $package->name }}</h1>
    <p class="text-sm text-gray-500">Update subscription plan details</p>
</div>
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/admin/packages/{{ $package->id }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
            <input type="text" name="name" value="{{ old('name', $package->name) }}" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('description', $package->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $package->price) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (days)</label>
                <input type="number" name="duration_days" min="1" value="{{ old('duration_days', $package->duration_days) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Users</label>
                <input type="number" name="max_users" min="1" value="{{ old('max_users', $package->max_users) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Clients</label>
                <input type="number" name="max_clients" min="1" value="{{ old('max_clients', $package->max_clients) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Quotations</label>
                <input type="number" name="max_quotations" min="1" value="{{ old('max_quotations', $package->max_quotations) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Active
            </label>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Update Package</button>
            <a href="/admin/packages" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
