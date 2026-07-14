@extends('layouts.admin')
@section('title', 'Create Currency')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Currency</h1>
    <p class="text-sm text-gray-500">Add a new currency for quotations</p>
</div>
<form method="POST" action="/admin/currencies" class="max-w-xl">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency Code *</label>
            <input type="text" name="code" value="{{ old('code') }}" required maxlength="10"
                placeholder="USD" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none uppercase">
            <p class="text-xs text-gray-400 mt-1">ISO 4217 code, e.g. USD, EUR, GBP</p>
            @error('code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                placeholder="US Dollar" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Symbol *</label>
            <input type="text" name="symbol" value="{{ old('symbol') }}" required maxlength="10"
                placeholder="$" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('symbol')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
            <span class="text-sm text-gray-700">Set as default currency</span>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Create Currency</button>
        <a href="/admin/currencies" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
