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

class RecipientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_recipient_notifications(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $recipient = Recipient::factory()->create();

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::SENT->value,
            'created_at'   => now(),
        ]);

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::TELEGRAM->value,
            'status'       => NotificationStatusEnum::ERROR->value,
            'created_at'   => now(),
        ]);

        $response = $this->withToken($token)
            ->getJson("/api/v1/recipients/{$recipient->uuid}/notifications");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'channel', 'status', 'text'],
            ],
            'links',
            'meta',
        ]);
        $response->assertJsonCount(10, 'data');
    }
}
