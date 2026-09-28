<?php

namespace Tests\Feature;

use App\Models\KnowledgeItem;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Snippet;
use App\Models\Solution;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_many_projects()
    {
        $user = User::factory()->create();
        Project::factory(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->projects);
    }

    public function test_project_can_have_many_problems()
    {
        $project = Project::factory()->create();
        Problem::factory(2)->create(['project_id' => $project->id, 'user_id' => $project->user_id]);

        $this->assertCount(2, $project->problems);
    }

    public function test_problem_can_have_many_solutions()
    {
        $problem = Problem::factory()->create();
        Solution::factory(2)->create(['problem_id' => $problem->id, 'user_id' => $problem->user_id]);

        $this->assertCount(2, $problem->solutions);
    }

    public function test_deleting_user_cascades_to_all_resources()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $problem = Problem::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);
        Solution::factory()->create(['user_id' => $user->id, 'problem_id' => $problem->id]);
        Snippet::factory()->create(['user_id' => $user->id]);
        KnowledgeItem::factory()->create(['user_id' => $user->id]);
        Tag::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseCount('problems', 1);
        $this->assertDatabaseCount('solutions', 1);
        $this->assertDatabaseCount('snippets', 1);
        $this->assertDatabaseCount('knowledge_items', 1);
        $this->assertDatabaseCount('tags', 1);

        $user->delete();

        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('problems', 0);
        $this->assertDatabaseCount('solutions', 0);
        $this->assertDatabaseCount('snippets', 0);
        $this->assertDatabaseCount('knowledge_items', 0);
        $this->assertDatabaseCount('tags', 0);
    }
}

