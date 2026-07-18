<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Company $company) {
            \App\Services\FileCleanupService::deleteCompanyFiles($company);
        });
    }
    protected $fillable = ['name', 'email', 'phone', 'address', 'website', 'default_terms', 'logo', 'brand_color', 'brand_font', 'account_details', 'status'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function quotations(): HasManyThrough
    {
        return $this->hasManyThrough(Quotation::class, User::class);
    }

    public function clients()
    {
        return Client::whereIn('user_id', $this->users()->pluck('id'));
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

    public function canAddUser(): bool
    {
        $package = $this->package();
        if (!$package) return false;
        return $this->users()->count() < $package->max_users;
    }

    public function canAddClient(): bool
    {
        $package = $this->package();
        if (!$package) return false;
        return Client::whereIn('user_id', $this->users()->pluck('id'))->count() < $package->max_clients;
    }

    public function canAddQuotation(): bool
    {
        $package = $this->package();
        if (!$package) return false;
        return Quotation::whereIn('user_id', $this->users()->pluck('id'))->count() < $package->max_quotations;
    }

    public function userCount(): int
    {
        return $this->users()->count();
    }

    public function clientCount(): int
    {
        return Client::whereIn('user_id', $this->users()->pluck('id'))->count();
    }

    public function quotationCount(): int
    {
        return Quotation::whereIn('user_id', $this->users()->pluck('id'))->count();
    }
}
