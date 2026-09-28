<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
