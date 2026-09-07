<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:1000'],
            'guest_name' => [
                Rule::requiredIf(fn (): bool => ! $this->user()),
                'nullable',
                'string',
                'max:64',
            ],
        ];
    }
}
