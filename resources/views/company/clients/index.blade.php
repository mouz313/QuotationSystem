@extends('layouts.app')
@section('title', 'Clients')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Clients</h1>
        <p class="text-sm text-gray-500">Manage your client list</p>
    </div>
    <div class="flex gap-2">
        <a href="/clients/export" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Export CSV</a>
        <a href="/clients/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Client</a>
    </div>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Quotations</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($clients as $client)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $client->email }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $client->phone ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $client->quotations_count }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/clients/{{ $client->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        <form method="POST" action="/clients/{{ $client->id }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No clients yet. Add your first client!</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $clients->links() }}</div>
@endsection
