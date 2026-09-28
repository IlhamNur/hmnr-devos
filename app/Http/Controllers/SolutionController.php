<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Traits\RecordsActivity;

class SolutionController extends Controller
{
    use RecordsActivity;
    public function store(Request $request, Problem $problem)
    {
        Gate::authorize('view', $problem); // Owner of problem can add solution

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'solution_type' => 'required|in:explanation,code,configuration,workaround,documentation,other',
        ]);

        $solution = $problem->solutions()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'solution_type' => $validated['solution_type'],
            'is_verified' => false,
        ]);
        
        $this->recordActivity('created_solution', $problem, 'Added solution to "' . $problem->title . '"');

        return redirect()->route('problems.show', $problem)->with('status', 'Solution added successfully.');
    }

    public function verify(Problem $problem, Solution $solution)
    {
        Gate::authorize('update', $problem);
        
        if ($solution->problem_id !== $problem->id) {
            abort(404);
        }

        // Toggle verification status
        $solution->update([
            'is_verified' => !$solution->is_verified
        ]);

        $statusMessage = $solution->is_verified ? 'Solution marked as verified.' : 'Solution unverified.';

        return redirect()->route('problems.show', $problem)->with('status', $statusMessage);
    }

    public function destroy(Problem $problem, Solution $solution)
    {
        Gate::authorize('update', $problem);
        
        if ($solution->problem_id !== $problem->id) {
            abort(404);
        }

        $solution->delete();

        return redirect()->route('problems.show', $problem)->with('status', 'Solution removed successfully.');
    }
}
