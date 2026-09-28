<?php

namespace Database\Factories;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        $types = [
            'created_project' => 'Created project "%s"',
            'created_problem' => 'Logged problem "%s"',
            'solved_problem' => 'Solved "%s"',
            'created_solution' => 'Added solution to "%s"',
            'created_snippet' => 'Created snippet "%s"',
            'created_knowledge' => 'Added knowledge "%s"',
        ];

        $type = $this->faker->randomElement(array_keys($types));

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'subject_type' => Problem::class,
            'subject_id' => Problem::factory(),
            'description' => sprintf($types[$type], $this->faker->words(3, true)),
        ];
    }
}
