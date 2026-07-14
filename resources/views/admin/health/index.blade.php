@extends('layouts.admin')
@section('title', 'System Health')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">System Health</h1>
    <p class="text-sm text-gray-500">Server status and diagnostics</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">PHP Version</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['php_version'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Laravel Version</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['laravel_version'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Platform</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['os'] }} {{ $health['server_software'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Database</div>
        <div class="text-xl font-bold {{ $health['db_connected'] ? 'text-green-600' : 'text-red-600' }}">
            {{ $health['db_connected'] ? 'Connected' : 'Disconnected' }}
        </div>
        <div class="text-xs text-gray-400">{{ strtoupper($health['db_driver']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Cache</div>
        <div class="text-xl font-bold {{ $health['cache_working'] ? 'text-green-600' : 'text-red-600' }}">
            {{ $health['cache_working'] ? 'Working' : 'Error' }}
        </div>
        <div class="text-xs text-gray-400">{{ strtoupper($health['cache_driver']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Queue Driver</div>
        <div class="text-xl font-bold text-gray-800">{{ strtoupper($health['queue_driver']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Pusher Configured</div>
        <div class="text-xl font-bold {{ $health['pusher_configured'] ? 'text-green-600' : 'text-yellow-600' }}">
            {{ $health['pusher_configured'] ? 'Yes' : 'No' }}
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Storage Used</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['storage_used'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Log File Size</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['log_size'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Memory Limit</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['memory_limit'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <div class="text-xs text-gray-500 mb-1">Max Upload Size</div>
        <div class="text-xl font-bold text-gray-800">{{ $health['max_upload'] }}</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Database Table Counts</h2>
    </div>
    <table class="w-full text-sm">
        <thead><tr class="text-left text-gray-500 bg-gray-50">
            <th class="px-4 py-3">Table</th><th class="px-4 py-3 text-right">Rows</th>
        </tr></thead>
        <tbody>
        @foreach($health['table_counts'] as $table => $count)
            <tr class="border-t">
                <td class="px-4 py-3 font-mono text-sm">{{ $table }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ is_int($count) ? number_format($count) : $count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
