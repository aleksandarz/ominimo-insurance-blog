<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_a_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Guest attempt',
            'content' => 'Should be rejected.',
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_authenticated_user_can_create_a_post(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'My API Post',
            'content' => 'Created through the JSON API.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('posts', [
            'title' => 'My API Post',
            'user_id' => $user->id,
        ]);
    }

    public function test_non_owner_cannot_update_a_post(): void
    {
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->create());

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Hijacked',
            'content' => 'Not allowed.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['title' => 'Hijacked']);
    }

    public function test_owner_can_update_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated via API',
            'content' => 'New body.',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated via API']);
    }

    public function test_admin_can_delete_any_post(): void
    {
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->admin()->create());

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
