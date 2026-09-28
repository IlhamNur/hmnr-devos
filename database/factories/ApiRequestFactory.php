<?php

namespace Database\Factories;

use App\Models\ApiRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiRequest>
 */
class ApiRequestFactory extends Factory
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
            'method' => 'GET',
            'url' => 'https://api.example.com',
            'body_type' => 'none',
            'last_status' => 200,
        ];
    }
}
