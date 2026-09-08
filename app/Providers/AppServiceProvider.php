<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use App\Observers\CommentObserver;
use App\Observers\PostObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);

        RateLimiter::for('posts-write', function (Request $request): Limit {
            $identifier = $request->user()?->id ?: $request->ip();
            $key = $identifier.'|'.$request->userAgent();

            return Limit::perMinute(30)->by($key);
        });
    }
}
