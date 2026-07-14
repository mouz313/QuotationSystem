@extends('layouts.admin')
@section('title', 'Company Users')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Company Users</h1>
        <p class="text-sm text-gray-500">Manage company admin and staff accounts</p>
    </div>
    <a href="/admin/company-users/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Company User</a>
</div>

<form method="GET" action="/admin/company-users" class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
    </div>
    <div class="min-w-[180px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Company</label>
        <select name="company_id" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">All Companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[150px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
        <select name="role" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">All Roles</option>
            <option value="company_admin" {{ request('role') == 'company_admin' ? 'selected' : '' }}>Company Admin</option>
            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
        <a href="/admin/company-users" class="px-4 py-2 border text-sm rounded-lg hover:bg-gray-50">Clear</a>
    </div>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Company</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($users as $u)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                <td class="px-4 py-3">
                    @if($u->company)
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ $u->company->name }}</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($u->role === 'company_admin')
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Company Admin</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Staff</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="/admin/company-users/{{ $u->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="/admin/company-users/{{ $u->id }}" onsubmit="return confirm('Delete this company user?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No company users found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
