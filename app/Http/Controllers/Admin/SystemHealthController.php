<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SystemHealthController extends Controller
{
    public function index()
    {
        $dbConnected = true;
        try { DB::connection()->getPdo(); } catch (\Exception $e) { $dbConnected = false; }

        $tableCounts = [];
        if ($dbConnected) {
            $tables = ['users', 'companies', 'clients', 'items', 'quotations', 'quotation_items', 'packages', 'company_packages', 'settings', 'currencies', 'taxes', 'pages', 'activity_log', 'admin_roles'];
            foreach ($tables as $table) {
                try { $tableCounts[$table] = DB::table($table)->count(); } catch (\Exception $e) { $tableCounts[$table] = 'N/A'; }
            }
        }

        $storageUsed = 0;
        try {
            $files = Storage::allFiles('app');
            foreach ($files as $file) { $storageUsed += Storage::size($file); }
        } catch (\Exception $e) {}

        $logSize = 0;
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) { $logSize = filesize($logPath); }

        $health = [
            'php_version'      => PHP_VERSION,
            'laravel_version'  => app()->version(),
            'server_software'  => PHP_INT_SIZE === 64 ? '64-bit' : '32-bit',
            'os'               => PHP_OS,
            'db_connected'     => $dbConnected,
            'db_driver'        => config('database.default'),
            'cache_driver'     => config('cache.default'),
            'queue_driver'     => config('queue.default'),
            'cache_working'    => $this->testCache(),
            'table_counts'     => $tableCounts,
            'storage_used'     => $this->formatBytes($storageUsed),
            'log_size'         => $this->formatBytes($logSize),
            'memory_limit'     => ini_get('memory_limit'),
            'max_upload'       => ini_get('upload_max_filesize'),
            'pusher_configured' => !empty(config('broadcasting.connections.pusher.app_id')),
        ];

        return view('admin.health.index', compact('health'));
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return back()->with('success', 'Application cache, config, routes, and views cleared successfully.');
    }

    public function truncateLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return back()->with('success', 'Log file cleared successfully.');
    }

    private function testCache(): bool
    {
        try {
            Cache::put('__health_check', true, 10);
            return Cache::get('__health_check') === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
