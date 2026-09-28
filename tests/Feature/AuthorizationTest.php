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

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_a_cannot_access_user_bs_project()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $projectB = Project::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $projectB));
        $this->assertFalse($userA->can('view', $projectB));
        $this->assertFalse($userA->can('update', $projectB));
        $this->assertFalse($userA->can('delete', $projectB));
    }

    public function test_user_a_cannot_access_user_bs_problem()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $problemB = Problem::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $problemB));
        $this->assertFalse($userA->can('view', $problemB));
    }

    public function test_user_a_cannot_access_user_bs_solution()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $solutionB = Solution::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $solutionB));
        $this->assertFalse($userA->can('view', $solutionB));
    }

    public function test_user_a_cannot_access_user_bs_snippet()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $snippetB = Snippet::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $snippetB));
        $this->assertFalse($userA->can('view', $snippetB));
    }

    public function test_user_a_cannot_access_user_bs_knowledge()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $knowledgeB = KnowledgeItem::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $knowledgeB));
        $this->assertFalse($userA->can('view', $knowledgeB));
    }

    public function test_user_a_cannot_access_user_bs_tag()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $tagB = Tag::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $tagB));
        $this->assertFalse($userA->can('view', $tagB));
    }
    public function test_user_a_cannot_access_user_bs_api_request()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $apiRequestB = \App\Models\ApiRequest::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $apiRequestB));
        $this->assertFalse($userA->can('view', $apiRequestB));
        $this->assertFalse($userA->can('delete', $apiRequestB));
    }

    public function test_user_a_cannot_access_user_bs_mvel_execution()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $executionB = \App\Models\MvelExecution::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $executionB));
        $this->assertFalse($userA->can('view', $executionB));
        $this->assertFalse($userA->can('delete', $executionB));
    }

    public function test_user_a_cannot_access_user_bs_activity()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $activityB = \App\Models\Activity::factory()->create(['user_id' => $userB->id]);

        $this->assertTrue($userB->can('view', $activityB));
        $this->assertFalse($userA->can('view', $activityB));
    }
}

