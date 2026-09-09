<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\ChannelTypeEnum;
use App\Models\Notification;
use Exception;

interface NotificationRepositoryInterface
{
    public function create(array $data): Notification;

    /**
     * @throws Exception
     */
    public function getAddress(int $recipientId, ChannelTypeEnum $channel): string;
}
