@extends('layouts.admin')
@section('title', 'Activity Log')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Activity Log</h1>
    <p class="text-sm text-gray-500">Track all system actions and changes</p>
</div>
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Action</label>
            <select name="action" class="px-3 py-2 border rounded-lg text-sm outline-none">
                <option value="">All Actions</option>
                @foreach(['created','updated','deleted','status_changed','login','package_assigned'] as $a)
                    <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $a)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">User</label>
            <select name="user_id" class="px-3 py-2 border rounded-lg text-sm outline-none">
                <option value="">All Users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Time</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">IP</th>
        </tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->created_at->format('M d, Y H:i') }}</td>
                <td class="px-4 py-3 font-medium">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full {{ \App\Models\ActivityLog::getActionColor($log->action) }}">
                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $log->description ?? '-' }}</td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $log->ip_address ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
