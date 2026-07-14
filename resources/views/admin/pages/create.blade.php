@extends('layouts.admin')
@section('title', 'Create Page')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Page</h1>
</div>
<form method="POST" action="/admin/pages" class="max-w-3xl">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    oninput="document.getElementById('slugPreview').textContent = '/' + this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'')">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug (auto-generated)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                    placeholder="auto-from-title">
                <div class="text-xs text-gray-400 mt-1">Preview: <span id="slugPreview" class="font-mono">/</span></div>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
            <input type="text" name="meta_description" value="{{ old('meta_description') }}" maxlength="500"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                placeholder="SEO description">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content (HTML)</label>
            <textarea name="content" rows="16" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm">{{ old('content') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
            <span class="text-sm text-gray-700">Published</span>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 font-medium">Create Page</button>
        <a href="/admin/pages" class="px-5 py-2 border text-sm rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
