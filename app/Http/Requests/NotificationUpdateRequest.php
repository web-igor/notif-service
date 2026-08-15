<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(NotificationStatusEnum::class)],
        ];
    }
}
