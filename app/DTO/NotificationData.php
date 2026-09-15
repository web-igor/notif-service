<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;

readonly class NotificationData
{
    private function __construct(
        private string $recipientUuid,
        private ChannelTypeEnum $channel,
        private ?string $email,
        private ?string $telegramChatId,
        private string $text
    ) {
    }

    public static function fromRequest(array $data): self
    {
        $channel = ChannelTypeEnum::from($data['channel']);

        return new self(
            recipientUuid: $data['recipient_uuid'],
            channel: $channel,
            email: $data['email'] ?? null,
            telegramChatId: ! empty($data['telegram_chat_id']) ? (string) $data['telegram_chat_id'] : null,
            text: $data['text']
        );
    }

    public function getDataForCreate(): array
    {
        return [
            'recipientUuid'    => $this->recipientUuid,
            'channel'          => $this->channel,
            'email'            => $this->email,
            'telegram_chat_id' => $this->telegramChatId,
            'text'             => $this->text,
            'status'           => NotificationStatusEnum::PENDING,
        ];
    }
}
