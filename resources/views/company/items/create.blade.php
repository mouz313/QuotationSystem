@extends('layouts.app')
@section('title', 'Add Item')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-gray-800">Add Item</h1></div>
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/items" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('title') border-red-500 @enderror">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
            <input type="number" name="unit_price" step="0.01" min="0" value="{{ old('unit_price') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('unit_price') border-red-500 @enderror">
            @error('unit_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-2 pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Save Item</button>
            <a href="/items" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
