<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    public function problems(): MorphToMany
    {
        return $this->morphedByMany(Problem::class, 'taggable');
    }

    public function solutions(): MorphToMany
    {
        return $this->morphedByMany(Solution::class, 'taggable');
    }

    public function snippets(): MorphToMany
    {
        return $this->morphedByMany(Snippet::class, 'taggable');
    }

    public function knowledgeItems(): MorphToMany
    {
        return $this->morphedByMany(KnowledgeItem::class, 'taggable');
    }
}
