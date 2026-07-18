<?php

namespace App\Models;

use App\Services\FileCleanupService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Payment $payment) {
            FileCleanupService::deletePaymentProof($payment);
        });
    }
    protected $fillable = [
        'quotation_id', 'quotation_item_id', 'client_user_id', 'amount', 'payment_method', 'transaction_id', 'gateway_response', 'paid_via', 'proof', 'notes',
        'status', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'reviewed_at'      => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function quotationItem(): BelongsTo
    {
        return $this->belongsTo(QuotationItem::class);
    }

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
