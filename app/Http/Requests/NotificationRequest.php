<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\NotificationData;
use App\Enums\ChannelTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'channel'      => ['required', 'string', Rule::enum(ChannelTypeEnum::class)],
            'recipient_id' => 'required|int|exists:users,id',
            'text'         => 'required|string|max:500',
        ];
    }

    public function toDTO(): NotificationData
    {
        return NotificationData::fromRequest(
            $this->validated()
        );
    }
}
