<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QuotationStatusLog extends Model
{
    protected $fillable = [
        'quotation_id', 'from_status', 'to_status',
        'changed_by_type', 'changed_by_id', 'notes',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function changedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
