<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use App\Traits\RecordsActivity;

class ProblemController extends Controller
{
    use RecordsActivity;
    public function index(Request $request)
    {
        $query = auth()->user()->problems()->with('project');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('error_message', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($severity = $request->input('severity')) {
            if ($severity !== 'all') {
                $query->where('severity', $severity);
            }
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }

        $problems = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();
        $projects = auth()->user()->projects()->orderBy('name')->get();

        return view('problems.index', compact('problems', 'projects'));
    }

    public function create()
    {
        $projects = auth()->user()->projects()->orderBy('name')->get();
        return view('problems.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'required|string',
            'error_message' => 'nullable|string',
            'error_type' => 'nullable|string|max:255',
            'environment' => 'nullable|string',
            'severity' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,investigating,solved,archived',
            'occurred_at' => 'nullable|date',
            'tags' => 'nullable|string'
        ]);

        $solvedAt = $validated['status'] === 'solved' ? now() : null;

        $problem = auth()->user()->problems()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'project_id' => $validated['project_id'] ?? null,
            'description' => $validated['description'],
            'error_message' => $validated['error_message'] ?? null,
            'error_type' => $validated['error_type'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'severity' => $validated['severity'],
            'status' => $validated['status'],
            'occurred_at' => $validated['occurred_at'] ?? null,
            'solved_at' => $solvedAt,
        ]);

        $this->syncTags($problem, $validated['tags'] ?? '');
        
        $this->recordActivity('created_problem', $problem, 'Logged problem "' . $problem->title . '"');
        if ($solvedAt) {
            $this->recordActivity('solved_problem', $problem, 'Solved "' . $problem->title . '"');
        }

        return redirect()->route('problems.show', $problem)->with('status', 'Problem logged successfully.');
    }

    public function show(Problem $problem)
    {
        Gate::authorize('view', $problem);

        $problem->load(['project', 'tags', 'solutions' => function($q) {
            $q->orderByDesc('is_verified')->latest();
        }]);

        return view('problems.show', compact('problem'));
    }

    public function edit(Problem $problem)
    {
        Gate::authorize('update', $problem);
        $projects = auth()->user()->projects()->orderBy('name')->get();

        return view('problems.edit', compact('problem', 'projects'));
    }

    public function update(Request $request, Problem $problem)
    {
        Gate::authorize('update', $problem);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'required|string',
            'error_message' => 'nullable|string',
            'error_type' => 'nullable|string|max:255',
            'environment' => 'nullable|string',
            'severity' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,investigating,solved,archived',
            'occurred_at' => 'nullable|date',
            'tags' => 'nullable|string'
        ]);

        $solvedAt = $problem->solved_at;
        
        if ($validated['status'] === 'solved' && $problem->status !== 'solved') {
            $solvedAt = now();
        } elseif ($validated['status'] !== 'solved') {
            $solvedAt = null;
        }

        $problem->update([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'] ?? null,
            'description' => $validated['description'],
            'error_message' => $validated['error_message'] ?? null,
            'error_type' => $validated['error_type'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'severity' => $validated['severity'],
            'status' => $validated['status'],
            'occurred_at' => $validated['occurred_at'] ?? null,
            'solved_at' => $solvedAt,
        ]);

        $this->syncTags($problem, $validated['tags'] ?? '');
        
        if ($validated['status'] === 'solved' && $problem->wasChanged('status')) {
            $this->recordActivity('solved_problem', $problem, 'Solved "' . $problem->title . '"');
        }

        return redirect()->route('problems.show', $problem)->with('status', 'Problem updated successfully.');
    }

    public function destroy(Problem $problem)
    {
        Gate::authorize('delete', $problem);
        
        $problem->delete();

        return redirect()->route('problems.index')->with('status', 'Problem deleted successfully.');
    }

    private function syncTags(Problem $problem, string $tagsString)
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

        $problem->tags()->sync($tagIds);
    }
}
