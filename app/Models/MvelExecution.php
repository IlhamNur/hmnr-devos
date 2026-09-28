<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MvelExecution extends Model
{
    /** @use HasFactory<\Database\Factories\MvelExecutionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'variables' => 'array',
        'is_saved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
