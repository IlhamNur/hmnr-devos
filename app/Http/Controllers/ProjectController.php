<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use App\Traits\RecordsActivity;

class ProjectController extends Controller
{
    use RecordsActivity;

    public function index(Request $request)
    {
        $query = auth()->user()->projects()->withCount('problems');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,archived',
            'repository_url' => 'nullable|url|max:255',
            'environment' => 'nullable|string|max:255',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'tags' => 'nullable|string'
        ]);

        $project = auth()->user()->projects()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'repository_url' => $validated['repository_url'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'started_at' => $validated['started_at'] ?? null,
            'ended_at' => $validated['ended_at'] ?? null,
        ]);

        $this->syncTags($project, $validated['tags'] ?? '');
        
        $this->recordActivity('created_project', $project, 'Created project "' . $project->name . '"');

        return redirect()->route('projects.show', $project)->with('status', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load('tags');
        $project->loadCount([
            'problems',
            'problems as solved_problems_count' => function ($query) {
                $query->where('status', 'solved');
            },
            'snippets',
            'knowledgeItems'
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,archived',
            'repository_url' => 'nullable|url|max:255',
            'environment' => 'nullable|string|max:255',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'tags' => 'nullable|string'
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'repository_url' => $validated['repository_url'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'started_at' => $validated['started_at'] ?? null,
            'ended_at' => $validated['ended_at'] ?? null,
        ]);

        $this->syncTags($project, $validated['tags'] ?? '');
        
        $this->recordActivity('updated_project', $project, 'Updated project "' . $project->name . '"');

        return redirect()->route('projects.show', $project)->with('status', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Project deleted successfully.');
    }

    private function syncTags(Project $project, string $tagsString)
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

        $project->tags()->sync($tagIds);
    }
}
