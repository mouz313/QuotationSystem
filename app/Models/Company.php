<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'website', 'status'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function companyPackages(): HasMany
    {
        return $this->hasMany(CompanyPackage::class);
    }

    public function activePackage()
    {
        return $this->companyPackages()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('start_date')
            ->first();
    }

    public function package()
    {
        $active = $this->activePackage();
        return $active ? $active->package : null;
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
