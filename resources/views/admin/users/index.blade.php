@extends('layouts.admin')
@section('title', 'Admin Users')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Admin Users</h1>
        <p class="text-sm text-gray-500">Manage admin panel access</p>
    </div>
    <a href="/admin/users/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Admin User</a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Created</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($users as $u)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                <td class="px-4 py-3">
                    @if($u->adminRole)
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $u->adminRole->name }}</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Super Admin</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $u->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/admin/users/{{ $u->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="/admin/users/{{ $u->id }}" onsubmit="return confirm('Delete this admin user?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No admin users.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
