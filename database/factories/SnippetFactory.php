<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SnippetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'language' => $this->faker->randomElement(['php', 'javascript', 'python', 'java', 'sql', 'bash']),
            'code' => $this->faker->text(),
            'purpose' => $this->faker->sentence(),
            'environment' => $this->faker->randomElement(['laravel', 'node', 'spring', 'postgres']),
            'version' => $this->faker->semver(),
            'is_favorite' => $this->faker->boolean(20),
        ];
    }
}
