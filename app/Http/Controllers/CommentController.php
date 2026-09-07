<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        $data = $request->validated();

        $comment = $post->comments()->create([
            'comment' => $data['comment'],
            'guest_name' => auth()->check() ? null : $data['guest_name'],
        ]);
        $comment->user()->associate(auth()->user());
        $comment->save();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()
            ->with('success', 'Comment deleted.');
    }
}