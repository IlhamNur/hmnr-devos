<?php

namespace App\Services\Search;

use App\Models\KnowledgeItem;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Snippet;
use App\Models\Solution;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KeywordSearchService implements SearchInterface
{
    /**
     * Search across all entity types using LIKE keyword matching.
     * Compatible with both PostgreSQL and SQLite for testing.
     */
    public function search(int $userId, string $query, int $limit = 5): array
    {
        $terms = $this->extractTerms($query);

        if (empty($terms)) {
            return [
                'projects' => collect(),
                'problems' => collect(),
                'solutions' => collect(),
                'snippets' => collect(),
                'knowledge' => collect(),
            ];
        }

        return [
            'projects' => $this->searchProjects($userId, $terms, $limit),
            'problems' => $this->searchProblems($userId, $terms, $limit),
            'solutions' => $this->searchSolutions($userId, $terms, $limit),
            'snippets' => $this->searchSnippets($userId, $terms, $limit),
            'knowledge' => $this->searchKnowledge($userId, $terms, $limit),
        ];
    }

    /**
     * Find content related to a given entity by extracting keywords
     * from the entity's title, description, tags, and error type,
     * then searching across all other entities.
     */
    public function findRelated(int $userId, string $entityType, int $entityId, int $limit = 8): Collection
    {
        $keywords = $this->extractEntityKeywords($userId, $entityType, $entityId);

        if (empty($keywords)) {
            return collect();
        }

        $results = collect();

        // Search problems (exclude current if entity is a problem)
        $problems = Problem::where('user_id', $userId)
            ->when($entityType === 'problem', fn($q) => $q->where('id', '!=', $entityId))
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%")
                      ->orWhere('error_type', 'like', "%{$kw}%")
                      ->orWhere('error_message', 'like', "%{$kw}%");
                }
            })
            ->with('project')
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->toRelatedResult($p, 'problem', $keywords));

        $results = $results->merge($problems);

        // Search solutions
        $solutions = Solution::where('user_id', $userId)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('content', 'like', "%{$kw}%");
                }
            })
            ->with('problem')
            ->limit($limit)
            ->get()
            ->map(fn($s) => $this->toRelatedResult($s, 'solution', $keywords));

        $results = $results->merge($solutions);

        // Search snippets (exclude current if entity is a snippet)
        $snippets = Snippet::where('user_id', $userId)
            ->when($entityType === 'snippet', fn($q) => $q->where('id', '!=', $entityId))
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%")
                      ->orWhere('code', 'like', "%{$kw}%");
                }
            })
            ->limit($limit)
            ->get()
            ->map(fn($s) => $this->toRelatedResult($s, 'snippet', $keywords));

        $results = $results->merge($snippets);

        // Search knowledge (exclude current if entity is knowledge)
        $knowledge = KnowledgeItem::where('user_id', $userId)
            ->when($entityType === 'knowledge', fn($q) => $q->where('id', '!=', $entityId))
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('content', 'like', "%{$kw}%");
                }
            })
            ->limit($limit)
            ->get()
            ->map(fn($k) => $this->toRelatedResult($k, 'knowledge', $keywords));

        $results = $results->merge($knowledge);

        // Sort by relevance score and take top results
        return $results->sortByDesc('relevance_score')->take($limit)->values();
    }

    // ─── Private helpers ──────────────────────────────────

    private function searchProjects(int $userId, array $terms, int $limit): Collection
    {
        return Project::where('user_id', $userId)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%")
                      ->orWhere('environment', 'like', "%{$term}%");
                }
            })
            ->limit($limit)
            ->get();
    }

    private function searchProblems(int $userId, array $terms, int $limit): Collection
    {
        return Problem::where('user_id', $userId)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('title', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%")
                      ->orWhere('error_message', 'like', "%{$term}%")
                      ->orWhere('error_type', 'like', "%{$term}%");
                }
            })
            ->with('project')
            ->limit($limit)
            ->get();
    }

    private function searchSolutions(int $userId, array $terms, int $limit): Collection
    {
        return Solution::where('user_id', $userId)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('title', 'like', "%{$term}%")
                      ->orWhere('content', 'like', "%{$term}%");
                }
            })
            ->with('problem')
            ->limit($limit)
            ->get();
    }

    private function searchSnippets(int $userId, array $terms, int $limit): Collection
    {
        return Snippet::where('user_id', $userId)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('title', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%")
                      ->orWhere('code', 'like', "%{$term}%")
                      ->orWhere('language', 'like', "%{$term}%");
                }
            })
            ->limit($limit)
            ->get();
    }

    private function searchKnowledge(int $userId, array $terms, int $limit): Collection
    {
        return KnowledgeItem::where('user_id', $userId)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('title', 'like', "%{$term}%")
                      ->orWhere('content', 'like', "%{$term}%");
                }
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Extract search terms from raw query string.
     */
    private function extractTerms(string $query): array
    {
        return collect(explode(' ', trim($query)))
            ->map(fn($t) => trim($t))
            ->filter(fn($t) => mb_strlen($t) >= 2)
            ->unique()
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Extract keywords from an entity for related-content lookup.
     */
    private function extractEntityKeywords(int $userId, string $entityType, int $entityId): array
    {
        $keywords = [];

        switch ($entityType) {
            case 'problem':
                $entity = Problem::where('user_id', $userId)->with('tags')->find($entityId);
                if (!$entity) return [];
                $text = $entity->title . ' ' . ($entity->error_type ?? '') . ' ' . ($entity->environment ?? '');
                $keywords = $this->extractTerms($text);
                // Add tag names as keywords
                foreach ($entity->tags as $tag) {
                    $keywords[] = $tag->name;
                }
                break;

            case 'snippet':
                $entity = Snippet::where('user_id', $userId)->with('tags')->find($entityId);
                if (!$entity) return [];
                $text = $entity->title . ' ' . ($entity->language ?? '') . ' ' . ($entity->purpose ?? '');
                $keywords = $this->extractTerms($text);
                foreach ($entity->tags as $tag) {
                    $keywords[] = $tag->name;
                }
                break;

            case 'knowledge':
                $entity = KnowledgeItem::where('user_id', $userId)->with('tags')->find($entityId);
                if (!$entity) return [];
                $text = $entity->title;
                $keywords = $this->extractTerms($text);
                foreach ($entity->tags as $tag) {
                    $keywords[] = $tag->name;
                }
                break;
        }

        return array_unique(array_filter($keywords));
    }

    /**
     * Convert a model to a standardized related-result array with relevance scoring.
     */
    private function toRelatedResult($model, string $type, array $keywords): array
    {
        $title = match ($type) {
            'problem' => $model->title,
            'solution' => $model->title,
            'snippet' => $model->title,
            'knowledge' => $model->title,
        };

        // Simple keyword match relevance: count how many keywords appear in the title
        $titleLower = mb_strtolower($title);
        $matchCount = 0;
        foreach ($keywords as $kw) {
            if (str_contains($titleLower, mb_strtolower($kw))) {
                $matchCount++;
            }
        }

        $totalKeywords = max(count($keywords), 1);
        $ratio = $matchCount / $totalKeywords;

        if ($ratio >= 0.6) {
            $relevanceLabel = 'Highly related';
        } elseif ($ratio >= 0.3) {
            $relevanceLabel = 'Related';
        } else {
            $relevanceLabel = 'Possible match';
        }

        return [
            'id' => $model->id,
            'type' => $type,
            'title' => $title,
            'status' => match ($type) {
                'problem' => $model->status,
                'solution' => $model->is_verified ? 'verified' : 'unverified',
                default => null,
            },
            'context' => match ($type) {
                'problem' => $model->project?->name,
                'solution' => $model->problem?->title,
                'snippet' => strtoupper($model->language ?? ''),
                'knowledge' => ucfirst($model->type ?? ''),
            },
            'relevance_label' => $relevanceLabel,
            'relevance_score' => $ratio,
            'url' => match ($type) {
                'problem' => route('problems.show', $model->id),
                'solution' => $model->problem_id ? route('problems.show', $model->problem_id) : '#',
                'snippet' => route('snippets.show', $model->id),
                'knowledge' => route('knowledge.show', $model->id),
            },
        ];
    }
}

