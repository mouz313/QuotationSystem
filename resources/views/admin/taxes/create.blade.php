@extends('layouts.admin')
@section('title', 'Create Tax')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Tax</h1>
    <p class="text-sm text-gray-500">Add a new tax rate for quotations</p>
</div>
<form method="POST" action="/admin/taxes" class="max-w-xl">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                placeholder="VAT" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Percentage (%) *</label>
            <input type="number" name="percentage" value="{{ old('percentage') }}" required min="0" max="100" step="0.01"
                placeholder="10" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('percentage')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
            <span class="text-sm text-gray-700">Set as default tax</span>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Create Tax</button>
        <a href="/admin/taxes" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
