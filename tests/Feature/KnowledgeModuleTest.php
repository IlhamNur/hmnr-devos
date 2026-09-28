<?php

namespace Tests\Feature;

use App\Models\KnowledgeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_knowledge_with_filters()
    {
        $user = User::factory()->create();
        KnowledgeItem::factory()->create(['user_id' => $user->id, 'title' => 'Architecture Docs', 'type' => 'documentation']);
        KnowledgeItem::factory()->create(['user_id' => $user->id, 'title' => 'Quick Note', 'type' => 'note']);

        $response = $this->actingAs($user)->get('/knowledge?type=documentation');
        $response->assertSee('Architecture Docs')->assertDontSee('Quick Note');
        
        $response = $this->actingAs($user)->get('/knowledge?search=Quick');
        $response->assertSee('Quick Note')->assertDontSee('Architecture Docs');
    }

    public function test_user_can_create_knowledge_item()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/knowledge', [
            'title' => 'Design Patterns',
            'type' => 'concept',
            'content' => '# Design Patterns\nList of patterns...',
            'tags' => 'architecture, patterns'
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('knowledge_items', [
            'title' => 'Design Patterns',
            'type' => 'concept'
        ]);

        $item = KnowledgeItem::where('title', 'Design Patterns')->first();
        $this->assertCount(2, $item->tags);
    }
    
    public function test_markdown_is_rendered_safely()
    {
        $user = User::factory()->create();
        $item = KnowledgeItem::factory()->create([
            'user_id' => $user->id,
            'title' => 'XSS Test',
            'content' => '<script>alert("xss")</script> **bold**',
        ]);

        $response = $this->actingAs($user)->get("/knowledge/{$item->id}");
        $response->assertStatus(200);
        
        // Str::markdown escapes HTML by default with our settings
        $response->assertDontSee('<script>alert("xss")</script>', false);
        $response->assertSee('alert("xss")', false);
    }

    public function test_unauthorized_access_prevented()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $itemA = KnowledgeItem::factory()->create(['user_id' => $userA->id]);

        $this->actingAs($userB)->get("/knowledge/{$itemA->id}")->assertStatus(403);
        $this->actingAs($userB)->put("/knowledge/{$itemA->id}", ['title' => 'Hacked', 'type' => 'note', 'content' => 'Hacked'])->assertStatus(403);
        $this->actingAs($userB)->delete("/knowledge/{$itemA->id}")->assertStatus(403);
    }
}
