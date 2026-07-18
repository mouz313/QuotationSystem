<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'address', 'client_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
