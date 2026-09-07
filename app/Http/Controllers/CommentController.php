<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        $comment = $post->comments()->make([
            'comment' => $data['comment'],
            'guest_name' => $request->user() ? null : ($data['guest_name'] ?? null),
        ]);
        $comment->user()->associate($request->user());
        $comment->save();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()
            ->with('success', 'Comment deleted.');
    }
}
