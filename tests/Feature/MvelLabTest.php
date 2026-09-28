<?php

namespace Tests\Feature;

use App\Models\MvelExecution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MvelLabTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_mvel_lab_and_history()
    {
        MvelExecution::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('mvel-lab.index'));

        $response->assertStatus(200);
        $response->assertViewHas('history');
        $this->assertCount(2, $response->viewData('history'));
    }

    public function test_can_execute_mvel_expression_and_store_history()
    {
        Http::fake([
            '*' => Http::response([
                'result' => 'Ilham',
                'type' => 'java.lang.String',
                'execution_time' => 12
            ], 200)
        ]);

        $payload = [
            'expression' => 'employee.name',
            'variables' => '{"employee": {"name": "Ilham"}}',
        ];

        $response = $this->actingAs($this->user)->postJson(route('mvel-lab.execute'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('result.result', 'Ilham');
        $response->assertJsonPath('result.result_type', 'java.lang.String');

        $this->assertDatabaseHas('mvel_executions', [
            'user_id' => $this->user->id,
            'expression' => 'employee.name',
            'result' => 'Ilham',
        ]);
    }

    public function test_mvel_execution_handles_errors()
    {
        Http::fake([
            '*' => Http::response([
                'error' => 'Property or field not found: employee'
            ], 400)
        ]);

        $payload = [
            'expression' => 'employee.name',
            'variables' => '{}',
        ];

        $response = $this->actingAs($this->user)->postJson(route('mvel-lab.execute'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('result.error', 'Property or field not found: employee');

        $this->assertDatabaseHas('mvel_executions', [
            'user_id' => $this->user->id,
            'error' => 'Property or field not found: employee',
        ]);
    }

    public function test_mvel_execution_handles_timeout_connection_error()
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $payload = [
            'expression' => 'while(true) {}',
            'variables' => '{}',
        ];

        $response = $this->actingAs($this->user)->postJson(route('mvel-lab.execute'), $payload);

        $response->assertStatus(200);
        $this->assertStringContainsString('Connection to MVEL executor failed', $response->json('result.error'));
    }

    public function test_can_delete_execution_history()
    {
        $execution = MvelExecution::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('mvel-lab.destroy', $execution));

        $response->assertRedirect(route('mvel-lab.index'));
        $this->assertDatabaseMissing('mvel_executions', ['id' => $execution->id]);
    }

    public function test_unauthorized_user_cannot_delete_history()
    {
        $otherUser = User::factory()->create();
        $execution = MvelExecution::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('mvel-lab.destroy', $execution));

        $response->assertStatus(403);
    }
}

