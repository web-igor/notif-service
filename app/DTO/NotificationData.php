<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;

readonly class NotificationData
{
    private function __construct(
        private int $recipientId,
        private ChannelTypeEnum $channel,
        private string $text
    ) {
    }

    public static function fromRequest(array $data): self
    {
        $channel = ChannelTypeEnum::from($data['channel']);

        return new self(
            recipientId: $data['recipient_id'],
            channel: $channel,
            text: $data['text']
        );
    }

    public function getDataForCreate(): array
    {
        return [
            'recipient_id' => $this->recipientId,
            'channel'      => $this->channel,
            'text'         => $this->text,
            'status'       => NotificationStatusEnum::PENDING,
        ];
    }
}
