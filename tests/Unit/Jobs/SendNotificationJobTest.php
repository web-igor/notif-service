<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;
use Exception;

class SendNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Throwable
     */
    public function test_job_handles_successful_send(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'recipient_id' => $user->id,
        ]);

        $service = new NotificationService();
        $job = new SendNotificationJob($notification, $service);
        $job->handle();

        $this->assertEquals(NotificationStatusEnum::SENT, $notification->fresh()->status);
    }

    /**
     * @throws Throwable
     */
    public function test_job_throws_exception_on_failure(): void
    {
        $user = User::factory()->create([
            'email' => null,
        ]);

        $notification = Notification::factory()->create([
            'recipient_id' => $user->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::PENDING->value,
        ]);

        $service = new NotificationService();
        $job = new SendNotificationJob($notification, $service);

        $this->expectException(Exception::class);

        $job->handle();
    }
}
