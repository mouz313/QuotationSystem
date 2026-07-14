@extends('layouts.admin')
@section('title', $company->name)
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $company->name }}</h1>
        <p class="text-sm text-gray-500">{{ $company->email }}</p>
    </div>
    <div class="flex gap-2">
        <a href="/admin/companies" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Status</div>
        <div class="mt-1">
            @if($company->status === 'active')<span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">Active</span>
            @elseif($company->status === 'blocked')<span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700">Blocked</span>
            @else<span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Active Package</div>
        @php $activePkg = $company->companyPackages->where('status', 'active')->where('end_date', '>=', now())->first(); @endphp
        <div class="mt-1 font-semibold">{{ $activePkg?->package?->name ?? 'No package' }}</div>
        @if($activePkg)
            <div class="text-xs text-gray-400">Expires: {{ $activePkg->end_date->format('M d, Y') }}</div>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Total Users</div>
        <div class="mt-1 font-semibold">{{ $company->users_count ?? $company->users->count() }}</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Assign Package</h2>
    <form method="POST" action="/admin/companies/{{ $company->id }}/assign-package" class="flex gap-3 items-end">
        @csrf
        <div class="flex-1">
            <label class="block text-sm text-gray-600 mb-1">Package</label>
            <select name="package_id" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}">{{ $pkg->name }} - ${{ $pkg->price }}/{{ $pkg->duration_days }}d</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ now()->toDateString() }}" required
                class="px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Assign</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Users</h2>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Name</th><th class="pb-2">Email</th><th class="pb-2">Role</th></tr></thead>
        <tbody>
        @foreach($company->users as $user)
            <tr class="border-b">
                <td class="py-2 font-medium">{{ $user->name }}</td>
                <td class="py-2 text-gray-600">{{ $user->email }}</td>
                <td class="py-2"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $user->role }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
