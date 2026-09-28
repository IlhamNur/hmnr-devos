<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\KnowledgeItem;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_displays_statistics()
    {
        // Active projects
        Project::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => 'active']);
        Project::factory()->count(1)->create(['user_id' => $this->user->id, 'status' => 'archived']);

        // Open problems
        $project = Project::factory()->create(['user_id' => $this->user->id, 'status' => 'active']);
        Problem::factory()->count(3)->create(['user_id' => $this->user->id, 'status' => 'open', 'project_id' => $project->id]);
        Problem::factory()->count(1)->create(['user_id' => $this->user->id, 'status' => 'solved', 'project_id' => $project->id]);

        // Snippets and knowledge
        Snippet::factory()->count(4)->create(['user_id' => $this->user->id]);
        KnowledgeItem::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Active Projects');
        
        $viewStats = $response->viewData('stats');
        $this->assertEquals(3, $viewStats['active_projects']);
        $this->assertEquals(3, $viewStats['open_problems']);
        $this->assertEquals(1, $viewStats['solved_problems']);
        $this->assertEquals(4, $viewStats['snippets']);
        $this->assertEquals(5, $viewStats['knowledge_items']);
    }

    public function test_activity_is_recorded_and_displayed()
    {
        // Create an activity directly
        $problem = Problem::factory()->create(['user_id' => $this->user->id, 'title' => 'Test Problem']);
        Activity::record('created_problem', $problem, 'Logged problem "Test Problem"');

        $response = $this->actingAs($this->user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Logged problem &quot;Test Problem&quot;', false);
    }

    public function test_quick_actions_and_command_palette_links_are_present()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('projects.create'));
        $response->assertSee(route('problems.create'));
        $response->assertSee(route('snippets.create'));
        $response->assertSee(route('knowledge.create'));
        
        // Command palette component should be rendered
        $response->assertSee('x-data="commandPalette()"', false);
    }
}
