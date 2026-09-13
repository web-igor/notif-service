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
            'recipient_uuid' => 'required|uuid',
            'channel'        => ['required', 'string', Rule::enum(ChannelTypeEnum::class)],
            'email'          => [
                'exclude_if:channel,' . ChannelTypeEnum::TELEGRAM->value,
                'required_if:channel,' . ChannelTypeEnum::EMAIL->value,
                'email',
            ],
            'telegram' => [
                'exclude_if:channel,' . ChannelTypeEnum::EMAIL->value,
                'required_if:channel,' . ChannelTypeEnum::TELEGRAM->value,
                'string',
            ],
            'text' => 'required|string|max:500',
        ];
    }

    public function toDTO(): NotificationData
    {
        return NotificationData::fromRequest(
            $this->validated()
        );
    }
}
