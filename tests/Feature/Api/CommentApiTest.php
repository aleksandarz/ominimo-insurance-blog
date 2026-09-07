<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_comment_is_stored_with_their_user_id(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/posts/{$post->id}/comments", [
            'comment' => 'From an account.',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.author_role', 'user');
        $response->assertJsonPath('data.is_guest', false);
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'guest_name' => null,
            'comment' => 'From an account.',
        ]);
    }

    public function test_guest_comment_is_stored_with_name_and_no_user_id(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'comment' => 'From a guest.',
            'guest_name' => 'Jane',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.author_role', 'guest');
        $response->assertJsonPath('data.is_guest', true);
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => null,
            'guest_name' => 'Jane',
        ]);
    }

    public function test_guest_comment_requires_a_name(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'comment' => 'No name given.',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('guest_name');
    }
}
