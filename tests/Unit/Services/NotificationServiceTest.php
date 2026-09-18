<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\NotificationData;
use App\Enums\AbilitiesEnum;
use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Client;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_sends_notification(): void
    {
        Queue::fake();

        $client = Client::factory()->create();

        $token = $client->createToken(
            'test-token',
            [AbilitiesEnum::SEND_EMAIL->value]
        );

        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer ' . $token->plainTextToken);
        app()->instance('request', $request);

        $client = $request->user();

        $dto = new NotificationData(
            recipientUuid: 'test-uuid-123',
            channel: ChannelTypeEnum::EMAIL,
            email: 'test@mail.ru',
            telegramChatId: null,
            text: 'test message',
        );

        $service = app(NotificationService::class);
        $notification = $service->createAndSend($dto, $client);

        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals(ChannelTypeEnum::EMAIL, $notification->channel);
        $this->assertEquals(NotificationStatusEnum::PENDING, $notification->status);

        Queue::assertPushedOn('notifications', SendNotificationJob::class);
    }

    public function test_it_throws_when_token_lacks_channel_permission(): void
    {
        Queue::fake();

        $client = Client::factory()->create();

        $token = $client->createToken(
            'test-token',
            [AbilitiesEnum::SEND_TELEGRAM->value]
        );

        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer ' . $token->plainTextToken);
        app()->instance('request', $request);

        $client = $request->user();

        $dto = new NotificationData(
            recipientUuid: 'test-uuid-123',
            channel: ChannelTypeEnum::EMAIL,
            email: 'test@mail.ru',
            telegramChatId: null,
            text: 'test message',
        );

        $service = app(NotificationService::class);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Token does not have permission to send via this channel.');
        $service->createAndSend($dto, $client);
    }
}
