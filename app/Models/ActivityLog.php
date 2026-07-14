<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'description', 'meta', 'ip_address'];

    protected $casts = ['meta' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public static function log(string $action, ?Model $subject = null, ?string $description = null, ?array $meta = null): static
    {
        return static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'description'  => $description,
            'meta'         => $meta,
            'ip_address'   => Request::ip(),
        ]);
    }

    public static function getActionColor(string $action): string
    {
        return match($action) {
            'created'        => 'bg-green-100 text-green-700',
            'updated'        => 'bg-blue-100 text-blue-700',
            'deleted'        => 'bg-red-100 text-red-700',
            'status_changed' => 'bg-yellow-100 text-yellow-700',
            'login'          => 'bg-indigo-100 text-indigo-700',
            default          => 'bg-gray-100 text-gray-600',
        };
    }
}
