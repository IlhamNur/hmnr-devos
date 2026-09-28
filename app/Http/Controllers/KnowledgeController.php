<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use App\Traits\RecordsActivity;

class KnowledgeController extends Controller
{
    use RecordsActivity;
    public function index(Request $request)
    {
        $query = auth()->user()->knowledgeItems()->with('project');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->where('type', $type);
            }
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        $knowledgeItems = $query->orderBy('updated_at', 'desc')->paginate(12)->withQueryString();
        $projects = auth()->user()->projects()->orderBy('name')->get();

        return view('knowledge.index', compact('knowledgeItems', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = auth()->user()->projects()->orderBy('name')->get();
        return view('knowledge.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:note,documentation,reference,concept,tutorial',
            'content' => 'required|string',
            'source' => 'nullable|url|max:255',
            'tags' => 'nullable|string'
        ]);

        $item = auth()->user()->knowledgeItems()->create([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'] ?? null,
            'type' => $validated['type'],
            'content' => $validated['content'],
            'source' => $validated['source'] ?? null,
        ]);

        $this->syncTags($item, $validated['tags'] ?? '');
        
        $this->recordActivity('created_knowledge', $item, 'Added knowledge "' . $item->title . '"');

        return redirect()->route('knowledge.show', $item)->with('status', 'Knowledge entry saved successfully.');
    }

    public function show(KnowledgeItem $knowledge)
    {
        Gate::authorize('view', $knowledge);
        $knowledge->load(['project', 'tags']);
        
        $renderedContent = Str::markdown($knowledge->content, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        return view('knowledge.show', compact('knowledge', 'renderedContent'));
    }

    public function edit(KnowledgeItem $knowledge)
    {
        Gate::authorize('update', $knowledge);
        $projects = auth()->user()->projects()->orderBy('name')->get();
        return view('knowledge.edit', compact('knowledge', 'projects'));
    }

    public function update(Request $request, KnowledgeItem $knowledge)
    {
        Gate::authorize('update', $knowledge);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:note,documentation,reference,concept,tutorial',
            'content' => 'required|string',
            'source' => 'nullable|url|max:255',
            'tags' => 'nullable|string'
        ]);

        $knowledge->update([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'] ?? null,
            'type' => $validated['type'],
            'content' => $validated['content'],
            'source' => $validated['source'] ?? null,
        ]);

        $this->syncTags($knowledge, $validated['tags'] ?? '');

        return redirect()->route('knowledge.show', $knowledge)->with('status', 'Knowledge entry updated successfully.');
    }

    public function destroy(KnowledgeItem $knowledge)
    {
        Gate::authorize('delete', $knowledge);
        $knowledge->delete();
        return redirect()->route('knowledge.index')->with('status', 'Knowledge entry deleted successfully.');
    }

    private function syncTags(KnowledgeItem $item, string $tagsString)
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

        $item->tags()->sync($tagIds);
    }
}
