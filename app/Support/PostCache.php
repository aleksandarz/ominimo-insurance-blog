<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;

/**
 * Read-through cache for the post feed and individual posts.
 *
 * The feed is cached per page under a "generational" key: every feed key
 * carries a version number, and a post write bumps that version so all
 * previously cached pages fall out of reach at once. This gives tag-like
 * invalidation on cache stores that do not support tags — including the
 * default `database` store.
 *
 * Caching happens at the query layer rather than on the HTTP response, so the
 * per-user authorization flags added by PostResource are still evaluated on
 * every request.
 */
final class PostCache
{
    /** How long a feed page or a single post stays cached, in seconds. */
    private const TTL = 900;

    private const VERSION_KEY = 'posts:feed:version';

    public static function feed(int $perPage = 10): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $key = sprintf('posts:feed:v%d:pp%d:p%d', self::version(), $perPage, $page);

        // The row collection and total are cached, not the paginator object:
        // the paginator carries request state (path, query) and is cheap to
        // rebuild, whereas re-running the query is what we want to avoid. The
        // cached models rely on config('cache.serializable_classes').
        ['items' => $items, 'total' => $total] = Cache::remember(
            $key,
            self::TTL,
            static function () use ($perPage, $page): array {
                $posts = Post::query()
                    ->with('user')
                    ->withCount('comments')
                    ->latest()
                    ->paginate($perPage, page: $page);

                return ['items' => $posts->getCollection(), 'total' => $posts->total()];
            },
        );

        // Rebuilt per request so pagination links target the current endpoint
        // (web and API share this cache entry).
        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    public static function post(int $id): Post
    {
        return Cache::remember("posts:show:{$id}", self::TTL, static fn (): Post => Post::query()
            ->with(['user', 'comments.user'])
            ->findOrFail($id));
    }

    /** Invalidate every cached feed page. */
    public static function flushFeed(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    /** Invalidate the cached detail view for a single post. */
    public static function forgetPost(int $id): void
    {
        Cache::forget("posts:show:{$id}");
    }

    /**
     * A comment was added or removed: refresh that post's detail view and the
     * feed (its cached comment counts are now stale).
     */
    public static function forgetForComment(Comment $comment): void
    {
        self::forgetPost($comment->post_id);
        self::flushFeed();
    }

    private static function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }
}
