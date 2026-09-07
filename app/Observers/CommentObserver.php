<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Comment;
use App\Support\PostCache;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        PostCache::forgetForComment($comment);
    }

    public function deleted(Comment $comment): void
    {
        PostCache::forgetForComment($comment);
    }
}
