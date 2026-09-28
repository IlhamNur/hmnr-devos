<?php

namespace Tests\Feature;

use App\Models\KnowledgeItem;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Snippet;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ── Global Search ────────────────────────────────────

    public function test_global_search_returns_grouped_results()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id, 'name' => 'MVEL Chatbot']);
        Problem::factory()->create(['user_id' => $this->user->id, 'title' => 'MVEL LinkedTreeMap casting', 'project_id' => $project->id]);
        Solution::factory()->create(['user_id' => $this->user->id, 'problem_id' => $project->problems->first()->id, 'title' => 'Convert MVEL map safely']);
        Snippet::factory()->create(['user_id' => $this->user->id, 'title' => 'MVEL nested map access', 'language' => 'mvel', 'code' => 'map.get("key")']);
        KnowledgeItem::factory()->create(['user_id' => $this->user->id, 'title' => 'MVEL expression handling', 'type' => 'reference', 'content' => 'Content here']);

        $response = $this->actingAs($this->user)->get('/search?q=MVEL');

        $response->assertStatus(200);
        $response->assertSee('MVEL Chatbot');
        $response->assertSee('MVEL LinkedTreeMap casting');
        $response->assertSee('Convert MVEL map safely');
        $response->assertSee('MVEL nested map access');
        $response->assertSee('MVEL expression handling');
    }

    public function test_search_with_empty_query_shows_empty_state()
    {
        $response = $this->actingAs($this->user)->get('/search?q=');
        $response->assertStatus(200);
        $response->assertSee('Search everything');
    }

    public function test_search_with_no_results_shows_no_results()
    {
        $response = $this->actingAs($this->user)->get('/search?q=xyznonexistent');
        $response->assertStatus(200);
        $response->assertSee('No results found');
    }

    public function test_search_only_returns_own_content()
    {
        $otherUser = User::factory()->create();
        Problem::factory()->create(['user_id' => $otherUser->id, 'title' => 'Secret MVEL bug']);
        Problem::factory()->create(['user_id' => $this->user->id, 'title' => 'My MVEL issue']);

        $response = $this->actingAs($this->user)->get('/search?q=MVEL');

        $response->assertStatus(200);
        $response->assertSee('My MVEL issue');
        $response->assertDontSee('Secret MVEL bug');
    }

    public function test_search_json_api()
    {
        Problem::factory()->create(['user_id' => $this->user->id, 'title' => 'API test problem']);

        $response = $this->actingAs($this->user)
            ->getJson('/search?q=API');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'query',
            'total',
            'results' => ['projects', 'problems', 'solutions', 'snippets', 'knowledge'],
        ]);
        $response->assertJsonFragment(['query' => 'API']);
    }

    // ── Related Content ──────────────────────────────────

    public function test_related_content_for_problem()
    {
        $problem1 = Problem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'MVEL LinkedTreeMap casting error',
            'error_type' => 'ClassCastException'
        ]);
        $problem2 = Problem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'MVEL map handling issue',
        ]);
        Snippet::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'MVEL nested casting',
            'language' => 'mvel',
            'code' => 'test'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/search/related?type=problem&id=' . $problem1->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['results']);

        // Should find related items but not itself
        $titles = collect($response->json('results'))->pluck('title')->toArray();
        $this->assertNotContains('MVEL LinkedTreeMap casting error', $titles);
    }

    public function test_related_content_respects_authorization()
    {
        $otherUser = User::factory()->create();
        $otherProblem = Problem::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other MVEL issue']);

        $problem = Problem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'MVEL casting issue'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/search/related?type=problem&id=' . $problem->id);

        $response->assertStatus(200);

        // Other user's content should not appear
        $titles = collect($response->json('results'))->pluck('title')->toArray();
        $this->assertNotContains('Other MVEL issue', $titles);
    }

    // ── Relevance Labels ─────────────────────────────────

    public function test_related_results_have_relevance_labels()
    {
        $problem = Problem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'PostgreSQL connection refused',
        ]);
        Problem::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'PostgreSQL timeout error',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/search/related?type=problem&id=' . $problem->id);

        $results = $response->json('results');
        if (count($results) > 0) {
            $this->assertArrayHasKey('relevance_label', $results[0]);
            $this->assertContains($results[0]['relevance_label'], ['Highly related', 'Related', 'Possible match']);
        }
    }

    // ── Signature Feature (How did I solve this?) ────────

    public function test_problem_show_has_search_link_when_unsolved()
    {
        $problem = Problem::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'title' => 'PostgreSQL refuses connections'
        ]);

        $response = $this->actingAs($this->user)->get('/problems/' . $problem->id);

        $response->assertStatus(200);
        $response->assertSee('How did I solve this?');
        // The link should point to search with the problem title
        $response->assertSee(route('search', ['q' => $problem->title]), false);
    }

    public function test_problem_show_hides_search_link_when_solved()
    {
        $problem = Problem::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'solved',
            'title' => 'PostgreSQL issue',
            'solved_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/problems/' . $problem->id);

        $response->assertStatus(200);
        $response->assertDontSee('How did I solve this?');
    }

    // ── Edge Cases ───────────────────────────────────────

    public function test_search_handles_short_query_gracefully()
    {
        $response = $this->actingAs($this->user)->get('/search?q=a');
        $response->assertStatus(200);
    }

    public function test_related_rejects_invalid_entity_type()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/search/related?type=invalid&id=1');

        $response->assertStatus(422);
    }

    // ── Ctrl/Cmd+K Shortcut ──────────────────────────────

    public function test_layout_contains_search_form_and_shortcut()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('commandPalette()', false);
    }
}

