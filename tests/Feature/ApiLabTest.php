<?php

namespace Tests\Feature;

use App\Models\ApiRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiLabTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_api_lab_and_history()
    {
        ApiRequest::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('api-lab.index'));

        $response->assertStatus(200);
        $response->assertViewHas('history');
        $this->assertCount(3, $response->viewData('history'));
    }

    public function test_can_execute_api_request_and_store_history()
    {
        Http::fake([
            'example.com/*' => Http::response(['data' => 'success'], 200, ['Content-Type' => 'application/json'])
        ]);

        $payload = [
            'method' => 'POST',
            'url' => 'https://example.com/api',
            'headers' => [
                ['key' => 'Authorization', 'value' => 'Bearer token']
            ],
            'query_parameters' => [
                ['key' => 'page', 'value' => '1']
            ],
            'body_type' => 'json',
            'body' => '{"test": true}'
        ];

        $response = $this->actingAs($this->user)->postJson(route('api-lab.execute'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('result.status', 200);
        $response->assertJsonPath('result.body', '{"data":"success"}');

        $this->assertDatabaseHas('api_requests', [
            'user_id' => $this->user->id,
            'method' => 'POST',
            'url' => 'https://example.com/api',
            'last_status' => 200,
        ]);
    }

    public function test_ssrf_protection_blocks_internal_ips()
    {
        // Try localhost
        $payload = [
            'method' => 'GET',
            'url' => 'http://localhost/admin',
            'body_type' => 'none',
        ];

        $response = $this->actingAs($this->user)->postJson(route('api-lab.execute'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('result.error', 'Connection to internal or restricted network blocked (SSRF Protection).');

        // Try 169.254.169.254
        $payload['url'] = 'http://169.254.169.254/latest/meta-data/';
        $response = $this->actingAs($this->user)->postJson(route('api-lab.execute'), $payload);
        
        $response->assertStatus(200);
        $response->assertJsonPath('result.error', 'Connection to internal or restricted network blocked (SSRF Protection).');
        
        // Try 10.x
        $payload['url'] = 'http://10.0.0.1/admin';
        $response = $this->actingAs($this->user)->postJson(route('api-lab.execute'), $payload);
        
        $response->assertStatus(200);
        $response->assertJsonPath('result.error', 'Connection to internal or restricted network blocked (SSRF Protection).');
    }

    public function test_can_delete_request_history()
    {
        $apiRequest = ApiRequest::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('api-lab.destroy', $apiRequest));

        $response->assertRedirect(route('api-lab.index'));
        $this->assertDatabaseMissing('api_requests', ['id' => $apiRequest->id]);
    }

    public function test_unauthorized_user_cannot_delete_history()
    {
        $otherUser = User::factory()->create();
        $apiRequest = ApiRequest::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('api-lab.destroy', $apiRequest));

        $response->assertStatus(403);
    }
}

