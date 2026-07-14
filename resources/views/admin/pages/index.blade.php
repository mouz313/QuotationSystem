@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pages</h1>
        <p class="text-sm text-gray-500">Manage static frontend pages</p>
    </div>
    <a href="/admin/pages/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Page</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Title</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Updated</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($pages as $page)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $page->title }}</td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">/{{ $page->slug }}</td>
                <td class="px-4 py-3">
                    @if($page->is_published)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Published</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Draft</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $page->updated_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/admin/pages/{{ $page->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="/admin/pages/{{ $page->id }}" onsubmit="return confirm('Delete this page?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No pages yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
