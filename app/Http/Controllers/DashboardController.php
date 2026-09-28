<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Problem;
use App\Models\Snippet;
use App\Models\KnowledgeItem;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistics
        $stats = [
            'active_projects' => $user->projects()->where('status', 'active')->count(),
            'open_problems' => $user->problems()->whereIn('status', ['open', 'investigating'])->count(),
            'solved_problems' => $user->problems()->where('status', 'solved')->count(),
            'snippets' => $user->snippets()->count(),
            'knowledge_items' => $user->knowledgeItems()->count(),
        ];

        // Recent content
        $recentProblems = $user->problems()->orderBy('updated_at', 'desc')->take(5)->get();
        $activeProjects = $user->projects()->where('status', 'active')->withCount(['problems', 'problems as solved_problems_count' => function ($q) {
            $q->where('status', 'solved');
        }])->orderBy('updated_at', 'desc')->take(3)->get();
        $recentSnippets = $user->snippets()->orderBy('updated_at', 'desc')->take(5)->get();
        $recentKnowledge = $user->knowledgeItems()->orderBy('updated_at', 'desc')->take(5)->get();

        // Activity Feed
        $activities = $user->activities()->with('subject')->orderBy('created_at', 'desc')->take(10)->get();

        return view('dashboard', compact('stats', 'recentProblems', 'activeProjects', 'recentSnippets', 'recentKnowledge', 'activities'));
    }
}
