<?php

namespace App\Models;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    /** @use HasFactory<\Database\Factories\ProblemFactory> */
    use HasFactory, HasTags;

    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'slug',
        'description',
        'error_message',
        'error_type',
        'environment',
        'severity',
        'status',
        'occurred_at',
        'solved_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'solved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }
}
