<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRevision extends Model
{
    protected $fillable = [
        'quotation_id', 'items_data', 'grand_total', 'discount_amount',
        'tax_percentage', 'tax_id', 'notes', 'created_by_type', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'items_data' => 'array',
            'grand_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
