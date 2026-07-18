<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentIntent extends Model
{
    protected $fillable = [
        'quotation_id', 'quotation_item_id', 'client_user_id',
        'amount', 'currency_code', 'gateway', 'gateway_intent_id',
        'status', 'gateway_response', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class);
    }
}
