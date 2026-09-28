<?php

namespace App\Http\Controllers;

use App\Services\Search\SearchInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SearchInterface $searchService
    ) {}

    /**
     * Global search page — shows grouped results across all entities.
     */
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));
        $results = [];
        $totalCount = 0;

        if (filled($query)) {
            $results = $this->searchService->search(auth()->id(), $query, 10);
            $totalCount = collect($results)->sum(fn($group) => $group->count());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'query' => $query,
                'total' => $totalCount,
                'results' => $results,
            ]);
        }

        return view('search.index', compact('query', 'results', 'totalCount'));
    }

    /**
     * Related content for a specific entity — used by problem/snippet/knowledge detail pages.
     */
    public function related(Request $request)
    {
        $entityType = $request->input('type');
        $entityId = (int) $request->input('id');

        if (!in_array($entityType, ['problem', 'snippet', 'knowledge'])) {
            return response()->json(['error' => 'Invalid entity type'], 422);
        }

        $related = $this->searchService->findRelated(auth()->id(), $entityType, $entityId, 8);

        if ($request->wantsJson()) {
            return response()->json(['results' => $related]);
        }

        return view('search.related', compact('related', 'entityType', 'entityId'));
    }
}

