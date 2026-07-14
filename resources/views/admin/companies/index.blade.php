@extends('layouts.admin')
@section('title', 'Companies')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Companies</h1>
        <p class="text-sm text-gray-500">Manage all tenant companies</p>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
            class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <select name="status" class="px-3 py-2 border rounded-lg text-sm outline-none">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Package</th><th class="px-4 py-3">Created</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($companies as $company)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium"><a href="/admin/companies/{{ $company->id }}" class="text-indigo-600 hover:underline">{{ $company->name }}</a></td>
                <td class="px-4 py-3 text-gray-600">{{ $company->email }}</td>
                <td class="px-4 py-3">
                    @if($company->status === 'active')<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @elseif($company->status === 'blocked')<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                    @else<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first()?->package?->name ?? 'None' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $company->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-1">
                        <form method="POST" action="/admin/companies/{{ $company->id }}/status">
                            @csrf @method('PATCH')
                            @if($company->status === 'active')
                                <input type="hidden" name="status" value="inactive">
                                <button class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Deactivate</button>
                            @else
                                <input type="hidden" name="status" value="active">
                                <button class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Activate</button>
                            @endif
                        </form>
                        @if($company->status !== 'blocked')
                        <form method="POST" action="/admin/companies/{{ $company->id }}/status">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="blocked">
                            <button class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Block</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No companies found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $companies->withQueryString()->links() }}</div>
@endsection
