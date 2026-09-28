<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use App\Traits\RecordsActivity;

class SnippetController extends Controller
{
    use RecordsActivity;
    public function index(Request $request)
    {
        $query = auth()->user()->snippets()->with('project');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($language = $request->input('language')) {
            if ($language !== 'all') {
                $query->where('language', $language);
            }
        }

        if ($request->boolean('favorite')) {
            $query->where('is_favorite', true);
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        $snippets = $query->orderBy('updated_at', 'desc')->paginate(12)->withQueryString();
        $projects = auth()->user()->projects()->orderBy('name')->get();

        return view('snippets.index', compact('snippets', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = auth()->user()->projects()->orderBy('name')->get();
        return view('snippets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'language' => 'required|string|max:100',
            'code' => 'required|string',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string|max:255',
            'environment' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:100',
            'tags' => 'nullable|string'
        ]);

        $snippet = auth()->user()->snippets()->create([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'] ?? null,
            'language' => $validated['language'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'version' => $validated['version'] ?? null,
            'is_favorite' => false,
        ]);

        $this->syncTags($snippet, $validated['tags'] ?? '');
        
        $this->recordActivity('created_snippet', $snippet, 'Created snippet "' . $snippet->title . '"');

        return redirect()->route('snippets.show', $snippet)->with('status', 'Snippet saved successfully.');
    }

    public function show(Snippet $snippet)
    {
        Gate::authorize('view', $snippet);
        $snippet->load(['project', 'tags']);
        return view('snippets.show', compact('snippet'));
    }

    public function edit(Snippet $snippet)
    {
        Gate::authorize('update', $snippet);
        $projects = auth()->user()->projects()->orderBy('name')->get();
        return view('snippets.edit', compact('snippet', 'projects'));
    }

    public function update(Request $request, Snippet $snippet)
    {
        Gate::authorize('update', $snippet);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'language' => 'required|string|max:100',
            'code' => 'required|string',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string|max:255',
            'environment' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:100',
            'tags' => 'nullable|string'
        ]);

        $snippet->update([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'] ?? null,
            'language' => $validated['language'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'version' => $validated['version'] ?? null,
        ]);

        $this->syncTags($snippet, $validated['tags'] ?? '');

        return redirect()->route('snippets.show', $snippet)->with('status', 'Snippet updated successfully.');
    }

    public function destroy(Snippet $snippet)
    {
        Gate::authorize('delete', $snippet);
        $snippet->delete();
        return redirect()->route('snippets.index')->with('status', 'Snippet deleted successfully.');
    }

    public function favorite(Snippet $snippet)
    {
        Gate::authorize('update', $snippet);
        $snippet->update(['is_favorite' => !$snippet->is_favorite]);
        
        $msg = $snippet->is_favorite ? 'Snippet added to favorites.' : 'Snippet removed from favorites.';
        return back()->with('status', $msg);
    }

    private function syncTags(Snippet $snippet, string $tagsString)
    {
        $tagNames = collect(explode(',', $tagsString))
            ->map(fn($t) => trim($t))
            ->filter(fn($t) => filled($t))
            ->unique();

        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = auth()->user()->tags()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $tagIds[] = $tag->id;
        }

        $snippet->tags()->sync($tagIds);
    }
}
