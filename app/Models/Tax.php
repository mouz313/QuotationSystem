<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tax extends Model
{
    protected $fillable = ['name', 'percentage', 'is_default', 'is_active'];

    protected $casts = [
        'percentage' => 'float',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->name . ' (' . $this->percentage . '%)';
    }
}
