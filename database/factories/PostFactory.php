<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'caption' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(640, 480, 'animals', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
