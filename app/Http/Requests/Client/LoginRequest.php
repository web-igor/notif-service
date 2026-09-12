<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Enums\ChannelTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'channels'   => 'sometimes|nullable|array',
            'channels.*' => ['distinct', Rule::enum(ChannelTypeEnum::class)],
            'email'      => 'required|email',
            'password'   => 'required|string|max:255',
        ];
    }
}
