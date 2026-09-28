<?php

namespace App\Models;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeItem extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgeItemFactory> */
    use HasFactory, HasTags;

    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'content',
        'type',
        'source',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
