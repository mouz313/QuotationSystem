@extends('layouts.app')
@section('title', 'Items')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Items</h1>
        <p class="text-sm text-gray-500">Service/product catalog</p>
    </div>
    <a href="/items/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Item</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Title</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Unit Price</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $item->title }}</td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $item->description ?? '-' }}</td>
                <td class="px-4 py-3 font-medium">${{ number_format($item->unit_price, 2) }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/items/{{ $item->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="/items/{{ $item->id }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No items yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
