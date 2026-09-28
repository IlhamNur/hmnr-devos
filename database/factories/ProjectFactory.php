<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'user_id' => User::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['active', 'paused', 'completed', 'archived']),
            'repository_url' => $this->faker->url(),
            'environment' => $this->faker->randomElement(['production', 'staging', 'development']),
            'started_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'ended_at' => null,
        ];
    }
}
