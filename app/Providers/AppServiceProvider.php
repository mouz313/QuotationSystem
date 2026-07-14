<?php

namespace App\Providers;

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
        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            $permissions = [];

            if ($user) {
                if ($user->isSuperAdmin()) {
                    // Super admins with no role get all permissions
                    $permissions = array_keys(\App\Models\AdminRole::allPermissions());
                } elseif ($user->adminRole) {
                    $permissions = $user->adminRole->permissions ?? [];
                }
            }

            $view->with('userPermissions', $permissions);
        });
    }
}
