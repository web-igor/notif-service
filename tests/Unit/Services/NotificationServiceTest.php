<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use App\DTO\NotificationData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_notification_and_dispatches_job(): void
    {
        Queue::fake();
        $service = new NotificationService();

        $user = User::factory()->create();

        $fakeRequest = [
            'recipient_id' => $user->id,
            'channel'      => ChannelTypeEnum::TELEGRAM->value,
            'text'         => 'Test text',
        ];

        $data = NotificationData::fromRequest($fakeRequest);

        $notification = $service->createAndSend($data);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals(1, $notification->recipient_id);
        $this->assertEquals(ChannelTypeEnum::TELEGRAM, $notification->channel);
        $this->assertEquals(NotificationStatusEnum::PENDING, $notification->status);

        Queue::assertPushed(SendNotificationJob::class);
    }
}
