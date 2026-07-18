<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id', 'package_id', 'amount', 'currency_code',
        'status', 'payment_method', 'transaction_id', 'gateway_response', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'gateway_response' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
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
}
