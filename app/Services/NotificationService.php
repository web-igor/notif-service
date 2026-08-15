<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\NotificationData;
use App\Enums\ChannelTypeEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use Exception;

readonly class NotificationService
{
    public function createAndSend(NotificationData $dto): Notification
    {
        $notification = $this->create($dto->getDataForCreate());
        SendNotificationJob::dispatch($notification, $this)->onQueue('notifications');

        return $notification;
    }

    /**
     * @throws Exception
     */
    public function getAddress(int $recipientId, ChannelTypeEnum $channel): string
    {
        $address = User::where('id', $recipientId)->value($channel->value);

        if (! $address) {
            throw new Exception('No address found for channel ' . $channel->value);
        }

        return $address;
    }

    private function create(array $data): Notification
    {
        return Notification::create($data);
    }
}
