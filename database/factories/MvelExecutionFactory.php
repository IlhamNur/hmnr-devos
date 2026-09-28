<?php

namespace Database\Factories;

use App\Models\MvelExecution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MvelExecution>
 */
class MvelExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'expression' => '1 + 1',
            'variables' => '{}',
            'result' => '2',
            'result_type' => 'java.lang.Integer',
        ];
    }
}
