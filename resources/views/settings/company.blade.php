@extends('layouts.app')
@section('title', 'Company Settings')
@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Company Settings</h1>
        <p class="text-sm text-gray-500">Manage your company profile and details</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Company Information</h2>
        <form method="POST" action="/company/settings" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" value="{{ old('website', $company->website) }}"
                        placeholder="https://..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('address', $company->address) }}</textarea>
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Save Changes</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-1">Subscription</h2>
        <div class="text-sm text-gray-500 mb-4">Your current plan and usage</div>
        @if($activePackage)
            @php $pkg = $activePackage->package; @endphp
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Plan:</span> <span class="ml-2 font-semibold">{{ $pkg->name }}</span></div>
                <div><span class="text-gray-500">Price:</span> <span class="ml-2">${{ number_format($pkg->price, 2) }}/{{ $pkg->duration_days }}d</span></div>
                <div><span class="text-gray-500">Started:</span> <span class="ml-2">{{ $activePackage->start_date->format('M d, Y') }}</span></div>
                <div><span class="text-gray-500">Expires:</span> <span class="ml-2">{{ $activePackage->end_date->format('M d, Y') }}</span></div>
                <div><span class="text-gray-500">Max Users:</span> <span class="ml-2">{{ $pkg->max_users }}</span></div>
                <div><span class="text-gray-500">Max Clients:</span> <span class="ml-2">{{ $pkg->max_clients }}</span></div>
                <div><span class="text-gray-500">Max Quotations:</span> <span class="ml-2">{{ $pkg->max_quotations }}</span></div>
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">{{ $activePackage->status }}</span>
                </div>
            </div>
        @else
            <p class="text-gray-500 text-sm">No active subscription. Contact admin to assign a package.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-1">Company Details</h2>
        <div class="text-sm text-gray-500 mb-4">Read-only information</div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Status:</span>
                @if($company->status === 'active')
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                @elseif($company->status === 'blocked')
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                @else
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                @endif
            </div>
            <div><span class="text-gray-500">Registered:</span> <span class="ml-2">{{ $company->created_at->format('M d, Y') }}</span></div>
        </div>
    </div>
</div>
@endsection
