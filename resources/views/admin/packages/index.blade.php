@extends('layouts.admin')
@section('title', 'Packages')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Packages</h1>
        <p class="text-sm text-gray-500">Manage subscription plans</p>
    </div>
    <a href="/admin/packages/create" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">+ New Package</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse($packages as $pkg)
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold">{{ $pkg->name }}</h3>
                @if($pkg->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">Inactive</span>
                @endif
            </div>
            <div class="text-3xl font-bold text-indigo-600 mb-1">${{ number_format($pkg->price, 2) }}<span class="text-sm font-normal text-gray-500">/{{ $pkg->duration_days }}d</span></div>
            <p class="text-sm text-gray-500 mb-4">{{ $pkg->description }}</p>
            <div class="text-sm text-gray-600 space-y-1 mb-4">
                <div>Users: <strong>{{ $pkg->max_users }}</strong></div>
                <div>Clients: <strong>{{ $pkg->max_clients }}</strong></div>
                <div>Quotations: <strong>{{ $pkg->max_quotations }}</strong></div>
            </div>
            <div class="text-xs text-gray-400 mb-3">{{ $pkg->company_packages_count }} active subscriptions</div>
            <div class="flex gap-2">
                <a href="/admin/packages/{{ $pkg->id }}/edit" class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Edit</a>
                <form method="POST" action="/admin/packages/{{ $pkg->id }}" onsubmit="return confirm('Delete this package?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-3 bg-white rounded-xl shadow p-8 text-center text-gray-400">No packages created yet.</div>
    @endforelse
</div>
@endsection
