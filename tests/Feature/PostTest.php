<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_posts_list(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertViewHas('posts');
    }

    public function test_guest_can_view_single_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee($post->title);
    }

    public function test_guest_cannot_access_create_page(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'My First Post',
            'content' => 'This is the content of my first post.',
        ]);

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'My First Post',
            'user_id' => $user->id,
        ]);
    }

    public function test_post_creation_requires_title_and_content(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_owner_can_update_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
    }

    public function test_non_owner_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        $response = $this->actingAs($other)->put(route('posts.update', $post), [
            'title' => 'Hacked Title',
            'content' => 'Hacked content.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['title' => 'Hacked Title']);
    }

    public function test_owner_can_delete_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}