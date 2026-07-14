@extends('layouts.admin')
@section('title', 'Taxes')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Taxes</h1>
        <p class="text-sm text-gray-500">Manage tax rates for quotations</p>
    </div>
    <a href="/admin/taxes/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Tax</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Percentage</th>
            <th class="px-4 py-3">Default</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($taxes as $tax)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $tax->name }}</td>
                <td class="px-4 py-3">{{ $tax->percentage }}%</td>
                <td class="px-4 py-3">
                    @if($tax->is_default)
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Default</span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($tax->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/admin/taxes/{{ $tax->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="/admin/taxes/{{ $tax->id }}" onsubmit="return confirm('Delete this tax?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No taxes created yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
