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
                    @if($user->id !== auth()->id())
                        <form method="POST" action="/company/users/{{ $user->id }}" onsubmit="return confirm('Remove this user?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Remove</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No team users.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
