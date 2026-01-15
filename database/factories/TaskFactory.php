<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'is_completed' => fake()->boolean(20),
            'due_date' => fake()->optional()->date(),
            'comments' => fake()->optional(0.3)->randomElements(
                [fake()->sentence(), fake()->sentence(), fake()->sentence()],
                fake()->numberBetween(0, 3)
            ) ?? [],
            'next_reminder' => fake()->optional(0.3)->dateTime(),
        ];
    }
}
