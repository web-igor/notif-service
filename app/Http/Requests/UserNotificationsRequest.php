<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\NotificationFilterData;
use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserNotificationsRequest extends FormRequest
{
    /**
     * Для тестового опускаем авторизацию
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page'     => 'sometimes|nullable|int|min:1',
            'per_page' => 'sometimes|nullable|int|min:1',
            'status'   => ['sometimes', 'nullable', 'string', Rule::enum(NotificationStatusEnum::class)],
            'channel'  => ['sometimes', 'nullable', 'string', Rule::enum(ChannelTypeEnum::class)],
        ];
    }

    public function toDTO(): NotificationFilterData
    {
        return NotificationFilterData::fromRequest(
            $this->validated()
        );
    }
}
