<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProblemFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence();
        
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(2, true),
            'error_message' => $this->faker->boolean(70) ? $this->faker->sentence() : null,
            'error_type' => $this->faker->randomElement(['Exception', 'TypeError', 'RuntimeError', 'DatabaseError']),
            'environment' => $this->faker->randomElement(['production', 'staging', 'development']),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['open', 'investigating', 'solved', 'archived']),
            'occurred_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'solved_at' => null,
        ];
    }
}
