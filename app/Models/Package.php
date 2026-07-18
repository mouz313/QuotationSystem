<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'price', 'currency_code', 'duration_days',
        'max_users', 'max_clients', 'max_quotations', 'is_active', 'sort_order', 'features'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency_code) {
            'PKR' => 'Rs',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };
    }

    public function companyPackages(): HasMany
    {
        return $this->hasMany(CompanyPackage::class);
    }
}
