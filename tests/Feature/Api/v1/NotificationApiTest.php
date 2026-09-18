<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Models\Notification;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_notification(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $email = 'test@mail.ru';
        $text = 'Test text';

        $response = $this->withToken($token)
            ->postJson('/api/v1/notifications', [
                'recipient_uuid' => '29c3343d-2283-47d3-9366-45f97ed5f86c',
                'channel'        => ChannelTypeEnum::EMAIL->value,
                'email'          => $email,
                'text'           => $text,
            ]);

        $response->assertStatus(201);

        $expectedStatus = config('queue.default') === 'sync' ?
            NotificationStatusEnum::SENT->value :
            NotificationStatusEnum::PENDING->value;

        $this->assertDatabaseHas('notifications', [
            'client_id' => $client->id,
            'channel'   => ChannelTypeEnum::EMAIL->value,
            'text'      => $text,
            'email'     => $email,
            'status'    => $expectedStatus,
        ]);
    }

    public function test_it_returns_notification(): void
    {
        $recipient = Recipient::factory()->create();
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $channels = array_column(ChannelTypeEnum::cases(), 'value');
        $notification = Notification::create([
            'recipient_id'     => $recipient->id,
            'client_id'        => $client->id,
            'channel'          => fake()->randomElement($channels),
            'status'           => NotificationStatusEnum::PENDING->value,
            'email'            => fake()->email,
            'telegram_chat_id' => (string) fake()->randomNumber(9, true),
            'text'             => fake()->text(500),
        ]);

        $response = $this->withToken($token)
            ->getJson("/api/v1/notifications/{$notification->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id'     => $notification->id,
                'status' => NotificationStatusEnum::PENDING->value,
            ],
        ]);
    }
}
