<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(200),
        ];
    }

    public function withReviews(): static
    {
        return $this->afterCreating(function (Profile $profile) {
            $profile->reviews()->saveMany(Review::factory(5)->make());
        });
    }
}
