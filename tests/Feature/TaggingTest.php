<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_can_be_attached_to_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $tag1 = Tag::factory()->create(['user_id' => $user->id]);
        $tag2 = Tag::factory()->create(['user_id' => $user->id]);

        $project->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $project->tags);
        $this->assertEquals($tag1->name, $project->tags->first()->name);
        
        // Test inverse
        $this->assertCount(1, $tag1->projects);
        $this->assertEquals($project->name, $tag1->projects->first()->name);
    }

    public function test_tags_can_be_attached_to_problem()
    {
        $user = User::factory()->create();
        $problem = Problem::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $problem->tags()->attach($tag->id);

        $this->assertCount(1, $problem->tags);
        $this->assertCount(1, $tag->problems);
    }
}

