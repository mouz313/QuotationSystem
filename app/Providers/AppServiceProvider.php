<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            $emailSettings = Setting::getGroup('email');
            if (!empty($emailSettings)) {
                $driver = $emailSettings['mail_driver'] ?? config('mail.default');
                config([
                    'mail.default'           => $driver,
                    'mail.mailers.smtp.host'       => $emailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port'       => $emailSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.username'   => $emailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password'   => $emailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.encryption' => $emailSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'mail.from.address'     => $emailSettings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name'        => $emailSettings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Exception $e) {
            // Table may not exist during migrations
        }

        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            $permissions = [];

            if ($user) {
                if ($user->isSuperAdmin()) {
                    $permissions = array_keys(\App\Models\AdminRole::allPermissions());
                } elseif ($user->adminRole) {
                    $permissions = $user->adminRole->permissions ?? [];
                }
            }

            $view->with('userPermissions', $permissions);
        });
    }
}
