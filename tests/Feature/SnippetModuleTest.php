<?php

namespace Tests\Feature;

use App\Models\Snippet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnippetModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_snippets_with_filters()
    {
        $user = User::factory()->create();
        Snippet::factory()->create(['user_id' => $user->id, 'title' => 'Alpha', 'language' => 'php', 'is_favorite' => true]);
        Snippet::factory()->create(['user_id' => $user->id, 'title' => 'Beta', 'language' => 'javascript', 'is_favorite' => false]);

        $response = $this->actingAs($user)->get('/snippets?language=php');
        $response->assertSee('Alpha')->assertDontSee('Beta');

        $response = $this->actingAs($user)->get('/snippets?favorite=1');
        $response->assertSee('Alpha')->assertDontSee('Beta');
        
        $response = $this->actingAs($user)->get('/snippets?search=Beta');
        $response->assertSee('Beta')->assertDontSee('Alpha');
    }

    public function test_user_can_create_snippet()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/snippets', [
            'title' => 'DB Connection',
            'language' => 'php',
            'code' => 'new PDO(...)',
            'purpose' => 'Connect to DB',
            'tags' => 'db, pdo'
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('snippets', [
            'title' => 'DB Connection',
            'language' => 'php'
        ]);

        $snippet = Snippet::where('title', 'DB Connection')->first();
        $this->assertCount(2, $snippet->tags);
    }

    public function test_user_can_toggle_favorite()
    {
        $user = User::factory()->create();
        $snippet = Snippet::factory()->create(['user_id' => $user->id, 'is_favorite' => false]);

        $this->actingAs($user)->patch("/snippets/{$snippet->id}/favorite");
        $this->assertTrue($snippet->refresh()->is_favorite);

        $this->actingAs($user)->patch("/snippets/{$snippet->id}/favorite");
        $this->assertFalse($snippet->refresh()->is_favorite);
    }

    public function test_unauthorized_access_prevented()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $snippetA = Snippet::factory()->create(['user_id' => $userA->id]);

        $this->actingAs($userB)->get("/snippets/{$snippetA->id}")->assertStatus(403);
        $this->actingAs($userB)->put("/snippets/{$snippetA->id}", ['title' => 'Hacked', 'language' => 'php', 'code' => 'test'])->assertStatus(403);
        $this->actingAs($userB)->delete("/snippets/{$snippetA->id}")->assertStatus(403);
        $this->actingAs($userB)->patch("/snippets/{$snippetA->id}/favorite")->assertStatus(403);
    }
}
