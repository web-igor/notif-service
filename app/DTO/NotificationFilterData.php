<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;

readonly class NotificationFilterData
{
    private function __construct(
        private ?NotificationStatusEnum $status,
        private ?ChannelTypeEnum $channel,
        private int $page,
        private int $perPage,
        private string $sortBy,
        private string $sortOrder,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            status: isset($data['status']) ? NotificationStatusEnum::from($data['status']) : null,
            channel: isset($data['channel']) ? ChannelTypeEnum::from($data['channel']) : null,
            page: isset($data['page']) ? (int) $data['page'] : 1,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 10,
            sortBy: 'id',
            sortOrder: 'desc',
        );
    }

    public function getDataForFilter(): array
    {
        return [
            'channel'   => $this->channel?->value,
            'status'    => $this->status?->value,
            'page'      => $this->page,
            'perPage'   => $this->perPage,
            'sortBy'    => $this->sortBy,
            'sortOrder' => $this->sortOrder,
        ];
    }
}
