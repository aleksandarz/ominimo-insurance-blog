<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Support\PostCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_is_served_from_cache_on_the_second_request(): void
    {
        Post::factory()->count(3)->create();

        $this->getJson('/api/posts')->assertOk();

        DB::enableQueryLog();
        $this->getJson('/api/posts')->assertOk();

        $touchedTable = collect(DB::getQueryLog())
            ->contains(fn (array $q): bool => str_contains($q['query'], '"posts"'));

        $this->assertFalse($touchedTable, 'A cached feed response must not query the posts table.');
    }

    public function test_web_and_api_feeds_share_the_same_cache_entry(): void
    {
        Post::factory()->count(3)->create();

        $this->get(route('posts.index'))->assertOk();

        DB::enableQueryLog();
        $this->getJson('/api/posts')->assertOk();

        $touchedTable = collect(DB::getQueryLog())
            ->contains(fn (array $q): bool => str_contains($q['query'], '"posts"'));

        $this->assertFalse($touchedTable, 'The API feed should reuse the cache warmed by the Blade feed.');
    }

    public function test_creating_a_post_invalidates_the_feed_cache(): void
    {
        Post::factory()->count(3)->create();
        $this->getJson('/api/posts')->assertJsonCount(3, 'data');

        Post::factory()->create(['title' => 'Fresh off the press']);

        $this->getJson('/api/posts')
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment(['title' => 'Fresh off the press']);
    }

    public function test_updating_a_post_refreshes_its_cached_detail_view(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['title' => 'Original']);

        $this->getJson("/api/posts/{$post->id}")->assertJsonPath('data.title', 'Original');

        $this->actingAs($user)->putJson("/api/posts/{$post->id}", [
            'title' => 'Rewritten',
            'content' => 'Updated body.',
        ])->assertOk();

        $this->getJson("/api/posts/{$post->id}")->assertJsonPath('data.title', 'Rewritten');
    }

    public function test_adding_a_comment_refreshes_the_cached_post_detail(): void
    {
        $post = Post::factory()->create();

        $this->getJson("/api/posts/{$post->id}")->assertJsonCount(0, 'data.comments');

        Comment::factory()->for($post)->create(['user_id' => null, 'guest_name' => 'Ada']);

        $this->getJson("/api/posts/{$post->id}")->assertJsonCount(1, 'data.comments');
    }

    public function test_feed_round_trips_through_a_serializing_cache_store(): void
    {
        config(['cache.default' => 'database']);
        Cache::store('database')->flush();

        Post::factory()->count(2)->create();

        PostCache::feed();
        $posts = PostCache::feed();

        $this->assertCount(2, $posts->items());
        $this->assertInstanceOf(Post::class, $posts->first());
        $this->assertTrue($posts->first()->relationLoaded('user'));
        $this->assertSame(0, $posts->first()->comments_count);
    }
}
