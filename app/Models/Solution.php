<?php

namespace App\Models;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solution extends Model
{
    /** @use HasFactory<\Database\Factories\SolutionFactory> */
    use HasFactory, HasTags;

    protected $fillable = [
        'user_id',
        'problem_id',
        'title',
        'content',
        'solution_type',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
