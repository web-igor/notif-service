<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Recipient;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Throwable;

class SendNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Throwable
     */
    public function test_job_handles_successful_send(): void
    {
        Http::fake();

        $recipient = Recipient::factory()->create();
        $client = Client::factory()->create();

        $notification = Notification::factory()->create([
            'recipient_id'     => $recipient->id,
            'client_id'        => $client->id,
            'channel'          => ChannelTypeEnum::TELEGRAM,
            'telegram_chat_id' => '123456',
        ]);

        $job = new SendNotificationJob($notification);
        $job->handle();

        $notification->refresh();

        $this->assertEquals(NotificationStatusEnum::SENT, $notification->status);
        Http::assertSent(function ($request) {
            return $request['chat_id'] === '123456';
        });
    }

    /**
     * @throws Throwable
     */
    public function test_job_handles_successful_email_send(): void
    {
        $recipient = Recipient::factory()->create();
        $client = Client::factory()->create();

        $notification = Notification::factory()->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL,
            'email'        => 'test@example.com',
        ]);

        $job = new SendNotificationJob($notification);
        $job->handle();

        $notification->refresh();

        $this->assertEquals(NotificationStatusEnum::SENT, $notification->status);
    }

    public function test_job_throws_exception_on_failure(): void
    {
        $recipient = Recipient::factory()->create();
        $client = Client::factory()->create();

        $notification = Notification::factory()->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => 'email',
            'email'        => null,
        ]);

        $job = new SendNotificationJob($notification);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The address field cannot be null.');

        $job->handle();
    }
}
