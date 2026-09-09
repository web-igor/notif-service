<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationRepositoryInterface;
use App\DTO\NotificationData;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Exception;

final readonly class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {
    }

    public function createAndSend(NotificationData $dto): Notification
    {
        $notification = $this->notificationRepository->create(
            $dto->getDataForCreate()
        );

        SendNotificationJob::dispatch($notification, $this)
            ->onQueue($notification::QUEUE_NAME);

        return $notification;
    }

    /**
     * @throws Exception
     */
    public function getAddress(int $recipientId): string
    {
        return $this->notificationRepository->getAddress($recipientId);
    }
}
