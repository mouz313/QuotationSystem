<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRole extends Model
{
    protected $fillable = ['name', 'permissions', 'is_default'];

    protected $casts = ['permissions' => 'array', 'is_default' => 'boolean'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'admin_role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return in_array('*', $this->permissions ?? []) || in_array($permission, $this->permissions ?? []);
    }

    public static function allPermissions(): array
    {
        return [
            'companies.manage'  => 'Manage Companies',
            'packages.manage'   => 'Manage Packages',
            'currencies.manage' => 'Manage Currencies',
            'taxes.manage'      => 'Manage Taxes',
            'settings.manage'   => 'Manage Settings',
            'quotations.view'   => 'View Quotations',
            'reports.view'      => 'View Reports',
            'users.manage'      => 'Manage Admin Users',
            'activity.view'     => 'View Activity Log',
            'health.view'       => 'View System Health',
            'pages.manage'      => 'Manage Pages',
            'email_templates.manage' => 'Manage Email Templates',
        ];
    }
}
