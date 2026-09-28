<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ApiRequestFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'query_parameters' => 'array',
        'headers' => 'array',
        'is_saved' => 'boolean',
        'last_response_headers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
