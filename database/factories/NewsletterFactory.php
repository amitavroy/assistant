<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Newsletter>
 */
class NewsletterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->unique()->uuid(),
            'subject' => fake()->sentence(),
            'from' => fake()->email(),
            'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'content' => fake()->paragraphs(3, true),
            'summary' => fake()->optional()->sentence(),
        ];
    }
}
