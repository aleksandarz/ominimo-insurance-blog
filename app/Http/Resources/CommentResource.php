<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'author_name' => $this->user?->name ?? $this->guest_name,
            'author_role' => $this->user?->role ?? UserRole::GUEST,
            'is_guest' => is_null($this->user_id),
            'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
