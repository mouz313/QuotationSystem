@extends('layouts.admin')
@section('title', 'Currencies')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Currencies</h1>
        <p class="text-sm text-gray-500">Manage available currencies for quotations</p>
    </div>
    <a href="/admin/currencies/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Currency</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Code</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Symbol</th>
            <th class="px-4 py-3">Default</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($currencies as $cur)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-mono font-semibold">{{ $cur->code }}</td>
                <td class="px-4 py-3">{{ $cur->name }}</td>
                <td class="px-4 py-3 text-lg">{{ $cur->symbol }}</td>
                <td class="px-4 py-3">
                    @if($cur->is_default)
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Default</span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($cur->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/admin/currencies/{{ $cur->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="/admin/currencies/{{ $cur->id }}" onsubmit="return confirm('Delete this currency?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No currencies created yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
