<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'subject_type',
        'subject_id',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record an activity for a user.
     */
    public static function record(string $type, Model $subject, string $description): self
    {
        return static::create([
            'user_id' => $subject->user_id,
            'type' => $type,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'description' => $description,
        ]);
    }
}
