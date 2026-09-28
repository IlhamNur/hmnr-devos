<?php

namespace App\Services\Search;

use Illuminate\Support\Collection;

interface SearchInterface
{
    /**
     * Search across all entity types for a given user.
     *
     * @param int $userId
     * @param string $query
     * @param int $limit Max results per entity type
     * @return array<string, Collection> Keyed by entity type
     */
    public function search(int $userId, string $query, int $limit = 5): array;

    /**
     * Find content related to a given entity.
     *
     * @param int $userId
     * @param string $entityType e.g. 'problem', 'snippet', 'knowledge'
     * @param int $entityId
     * @param int $limit
     * @return Collection
     */
    public function findRelated(int $userId, string $entityType, int $entityId, int $limit = 8): Collection;
}

