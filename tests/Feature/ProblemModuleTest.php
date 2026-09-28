<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Project;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_problems_with_pagination()
    {
        $user = User::factory()->create();
        Problem::factory(15)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/problems');

        $response->assertStatus(200);
        $response->assertViewHas('problems');
        $this->assertCount(10, $response->viewData('problems')->items());
    }

    public function test_user_can_create_problem_and_solved_at_is_set_if_status_is_solved()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/problems', [
            'title' => 'CSRF Token Mismatch',
            'project_id' => $project->id,
            'description' => 'Login fails with 419',
            'severity' => 'high',
            'status' => 'solved'
        ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('problems', [
            'title' => 'CSRF Token Mismatch',
            'status' => 'solved'
        ]);

        $problem = Problem::where('title', 'CSRF Token Mismatch')->first();
        $this->assertNotNull($problem->solved_at);
        $response->assertRedirect('/problems/' . $problem->id);
    }

    public function test_solved_at_cleared_if_status_changed_from_solved()
    {
        $user = User::factory()->create();
        $problem = Problem::factory()->create([
            'user_id' => $user->id,
            'status' => 'solved',
            'solved_at' => now()
        ]);

        $this->assertNotNull($problem->solved_at);

        $response = $this->actingAs($user)->put('/problems/' . $problem->id, [
            'title' => 'Still broken',
            'description' => 'Wait it is not solved',
            'severity' => 'critical',
            'status' => 'investigating'
        ]);

        $response->assertSessionHasNoErrors();

        $problem->refresh();
        $this->assertEquals('investigating', $problem->status);
        $this->assertNull($problem->solved_at);
    }

    public function test_user_can_add_multiple_solutions_to_problem()
    {
        $user = User::factory()->create();
        $problem = Problem::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post("/problems/{$problem->id}/solutions", [
            'title' => 'Solution 1',
            'content' => 'Just do this',
            'solution_type' => 'explanation'
        ])->assertSessionHasNoErrors();

        $this->actingAs($user)->post("/problems/{$problem->id}/solutions", [
            'title' => 'Solution 2',
            'content' => '<?php echo "fixed";',
            'solution_type' => 'code'
        ])->assertSessionHasNoErrors();

        $this->assertCount(2, $problem->solutions);
        $this->assertFalse($problem->solutions->first()->is_verified);
    }

    public function test_user_can_verify_and_unverify_solution()
    {
        $user = User::factory()->create();
        $problem = Problem::factory()->create(['user_id' => $user->id]);
        $solution = Solution::factory()->create([
            'user_id' => $user->id,
            'problem_id' => $problem->id,
            'is_verified' => false
        ]);

        // Verify
        $this->actingAs($user)->patch("/problems/{$problem->id}/solutions/{$solution->id}/verify");
        $this->assertTrue($solution->refresh()->is_verified);

        // Unverify
        $this->actingAs($user)->patch("/problems/{$problem->id}/solutions/{$solution->id}/verify");
        $this->assertFalse($solution->refresh()->is_verified);
    }

    public function test_unauthorized_access_prevented()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        
        $problemA = Problem::factory()->create(['user_id' => $userA->id]);
        $solutionA = Solution::factory()->create(['user_id' => $userA->id, 'problem_id' => $problemA->id]);

        // B tries to view A's problem
        $this->actingAs($userB)->get("/problems/{$problemA->id}")->assertStatus(403);
        
        // B tries to add solution to A's problem
        $this->actingAs($userB)->post("/problems/{$problemA->id}/solutions", [
            'title' => 'Hacked', 'content' => 'Hacked', 'solution_type' => 'other'
        ])->assertStatus(403);

        // B tries to verify A's solution
        $this->actingAs($userB)->patch("/problems/{$problemA->id}/solutions/{$solutionA->id}/verify")->assertStatus(403);
    }
}
