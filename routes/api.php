<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request): UserResource {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::middleware(['auth:sanctum', 'throttle:posts-write'])->prefix('posts')->controller(PostController::class)->group(function (): void {
    Route::post('/', 'store');
    Route::put('/{post}', 'update');
    Route::delete('/{post}', 'destroy');
});

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:posts-write');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
