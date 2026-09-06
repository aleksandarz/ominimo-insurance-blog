<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_comment_with_name(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('comments.store', $post), [
            'comment' => 'Nice post!',
            'guest_name' => 'John Doe',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'comment' => 'Nice post!',
            'guest_name' => 'John Doe',
            'user_id' => null,
        ]);
    }

    public function test_authenticated_user_can_comment_without_name(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'comment' => 'Great read.',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'guest_name' => null,
        ]);
    }

    public function test_comment_author_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_post_owner_can_delete_comment_on_their_post(): void
    {
        $postOwner = User::factory()->create();
        $post = Post::factory()->for($postOwner)->create();
        $comment = Comment::factory()->for($post)->create(['user_id' => null, 'guest_name' => 'Guest']);

        $response = $this->actingAs($postOwner)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_unrelated_user_cannot_delete_comment(): void
    {
        $commentAuthor = User::factory()->create();
        $randomUser = User::factory()->create();
        $comment = Comment::factory()->for($commentAuthor)->create();

        $response = $this->actingAs($randomUser)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}