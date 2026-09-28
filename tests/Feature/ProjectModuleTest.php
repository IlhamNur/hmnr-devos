<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_projects_with_pagination()
    {
        $user = User::factory()->create();
        Project::factory(15)->create(['user_id' => $user->id, 'status' => 'active']);

        $response = $this->actingAs($user)->get('/projects');

        $response->assertStatus(200);
        $response->assertViewHas('projects');
        $this->assertCount(10, $response->viewData('projects')->items());
    }

    public function test_user_can_filter_projects_by_status_and_search()
    {
        $user = User::factory()->create();
        Project::factory()->create(['user_id' => $user->id, 'name' => 'Alpha App', 'status' => 'active']);
        Project::factory()->create(['user_id' => $user->id, 'name' => 'Beta App', 'status' => 'completed']);

        $response = $this->actingAs($user)->get('/projects?status=completed');
        $response->assertSee('Beta App');
        $response->assertDontSee('Alpha App');

        $response = $this->actingAs($user)->get('/projects?search=Alpha');
        $response->assertSee('Alpha App');
        $response->assertDontSee('Beta App');
    }

    public function test_user_can_create_a_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'New Awesome Project',
            'description' => 'Description here',
            'status' => 'active',
            'environment' => 'production',
            'tags' => 'php, laravel, vue'
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'name' => 'New Awesome Project',
            'status' => 'active'
        ]);

        $project = Project::where('name', 'New Awesome Project')->first();
        $response->assertRedirect('/projects/' . $project->id);

        $this->assertCount(3, $project->tags);
    }

    public function test_user_can_update_a_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($user)->put('/projects/' . $project->id, [
            'name' => 'Updated Name',
            'status' => 'paused'
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
            'status' => 'paused'
        ]);

        $response->assertRedirect('/projects/' . $project->id);
    }

    public function test_user_can_delete_a_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/projects/' . $project->id);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $response->assertRedirect('/projects');
    }

    public function test_unauthorized_access_is_prevented()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $projectA = Project::factory()->create(['user_id' => $userA->id]);

        $responseShow = $this->actingAs($userB)->get('/projects/' . $projectA->id);
        $responseShow->assertStatus(403);

        $responseEdit = $this->actingAs($userB)->get('/projects/' . $projectA->id . '/edit');
        $responseEdit->assertStatus(403);

        $responseUpdate = $this->actingAs($userB)->put('/projects/' . $projectA->id, ['name' => 'Hacked', 'status' => 'active']);
        $responseUpdate->assertStatus(403);

        $responseDelete = $this->actingAs($userB)->delete('/projects/' . $projectA->id);
        $responseDelete->assertStatus(403);
    }

    public function test_project_statistics_are_calculated_correctly()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        // 3 problems total, 2 solved
        Problem::factory()->create(['user_id' => $user->id, 'project_id' => $project->id, 'status' => 'open']);
        Problem::factory()->create(['user_id' => $user->id, 'project_id' => $project->id, 'status' => 'solved']);
        Problem::factory()->create(['user_id' => $user->id, 'project_id' => $project->id, 'status' => 'solved']);

        $response = $this->actingAs($user)->get('/projects/' . $project->id);

        $response->assertStatus(200);
        $viewProject = $response->viewData('project');

        $this->assertEquals(3, $viewProject->problems_count);
        $this->assertEquals(2, $viewProject->solved_problems_count);
    }
}
