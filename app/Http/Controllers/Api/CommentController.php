<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): CommentResource
    {
        $data = $request->validated();

        $comment = $post->comments()->make([
            'comment' => $data['comment'],
            'guest_name' => $request->user() ? null : ($data['guest_name'] ?? null),
        ]);
        $comment->user()->associate($request->user());
        $comment->save();

        return new CommentResource($comment->load('user'));
    }

    public function destroy(Comment $comment): Response
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
