<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'duration_days',
        'max_users', 'max_clients', 'max_quotations', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function companyPackages(): HasMany
    {
        return $this->hasMany(CompanyPackage::class);
    }
}
