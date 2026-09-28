<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(4, true),
            'type' => $this->faker->randomElement(['note', 'documentation', 'reference', 'concept', 'tutorial']),
            'source' => $this->faker->url(),
        ];
    }
}
