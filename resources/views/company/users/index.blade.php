@extends('layouts.app')
@section('title', 'Team Users')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Team Users</h1>
        <p class="text-sm text-gray-500">Manage your company team members</p>
    </div>
    <a href="/company/users/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ Add User</a>
</div>
<form method="GET" action="/company/users" class="mb-4">
    <div class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <button class="px-4 py-2 bg-gray-100 text-sm rounded-lg hover:bg-gray-200">Search</button>
        @if(request('search'))
            <a href="/company/users" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </div>
</form>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Joined</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($users as $user)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    @if($user->role === 'company_admin')
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Admin</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Staff</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/company/users/{{ $user->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="/company/users/{{ $user->id }}" onsubmit="return confirm('Remove this user?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Remove</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ request('search') ? 'No users match your search.' : 'No team users.' }}</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
