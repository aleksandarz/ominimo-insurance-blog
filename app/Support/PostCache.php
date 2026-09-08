<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;

final class PostCache
{
    private const TTL = 900;

    private const VERSION_KEY = 'posts:feed:version';

    public static function feed(int $perPage = 10): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $key = sprintf('posts:feed:v%d:pp%d:p%d', self::version(), $perPage, $page);

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

    public static function flushFeed(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    public static function forgetPost(int $id): void
    {
        Cache::forget("posts:show:{$id}");
    }

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
