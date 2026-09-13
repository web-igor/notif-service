<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationStatusEnum;
use App\Factories\ChannelFactory;
use App\Models\Notification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60];

    public function __construct(
        private readonly Notification $notification
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $notification = $this->notification;

        if ($notification->status === NotificationStatusEnum::SENT) {
            return;
        }

        try {
            $channel = ChannelFactory::make($notification->channel);
            $address = $channel->getAddress($notification);

            if (! $address) {
                throw new Exception('The address field cannot be null.');
            }

            $result = $channel->send(
                $notification->recipient_id,
                $notification->text,
                $address
            );

            if (! $result) {
                throw new Exception('Error sending notification');
            }

            $notification->update(['status' => NotificationStatusEnum::SENT]);

        } catch (Throwable $ex) {
            Log::error('SendNotificationJob failed: ' . $ex->getMessage(), [
                'notification_id' => $notification->id,
            ]);

            throw $ex;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->notification->update(['status' => NotificationStatusEnum::ERROR]);
    }
}
