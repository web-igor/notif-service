<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationStatusEnum;
use App\Factories\ChannelFactory;
use App\Models\Notification;
use App\Services\NotificationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        private readonly Notification $notification,
        private readonly NotificationService $service,
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
            $recipientId = $notification->recipient_id;
            $address = $this->service->getAddress($recipientId, $notification->channel);
            $result = $channel->send($recipientId, $notification->text, $address);

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
