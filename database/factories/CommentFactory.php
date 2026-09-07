<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isGuest = fake()->boolean(30);

        return [
            'post_id' => Post::factory(),
            'user_id' => $isGuest ? null : User::factory(),
            'guest_name' => $isGuest ? fake()->name() : null,
            'comment' => fake()->sentence(12),
        ];
    }
}
