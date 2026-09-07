<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use App\Support\PostCache;

class PostObserver
{
    public function created(Post $post): void
    {
        PostCache::flushFeed();
    }

    public function updated(Post $post): void
    {
        PostCache::flushFeed();
        PostCache::forgetPost($post->id);
    }

    public function deleted(Post $post): void
    {
        PostCache::flushFeed();
        PostCache::forgetPost($post->id);
    }
}
