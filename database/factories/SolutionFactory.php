<?php

namespace Database\Factories;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolutionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'problem_id' => Problem::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'solution_type' => $this->faker->randomElement(['explanation', 'code', 'configuration', 'workaround', 'documentation', 'other']),
            'is_verified' => $this->faker->boolean(),
        ];
    }
}
