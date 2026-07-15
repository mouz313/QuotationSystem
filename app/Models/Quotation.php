<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'user_id', 'client_id', 'currency_id', 'tax_id',
        'quote_number', 'issue_date', 'expiry_date',
        'discount_amount', 'tax_percentage',
        'grand_total', 'status', 'terms_conditions',
        'payment_instructions',
        'payment_status', 'paid_amount', 'paid_at',
        'viewed_at',
    ];

    protected $casts = [
        'issue_date'     => 'date',
        'expiry_date'    => 'date',
        'paid_at'        => 'date',
        'viewed_at'      => 'datetime',
        'paid_amount'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(QuotationNote::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id', 'id')
            ->where('subject_type', static::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(QuotationStatusLog::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(QuotationRevision::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getPendingPaymentsAttribute()
    {
        return $this->payments()->where('status', 'pending')->get();
    }

    public function getCurrencySymbolAttribute(): string
    {
        return $this->currency ? $this->currency->symbol : '$';
    }
}
