<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_notification(): void
    {
        $user = User::factory()->create();
        $text = 'Test notification';

        $response = $this->postJson('/api/v1/notifications', [
            'recipient_id' => $user->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'text'         => $text,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('notifications', [
            'recipient_id' => $user->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'text'         => $text,
            'status'       => NotificationStatusEnum::PENDING->value,
        ]);
    }

    public function test_it_returns_notification_status(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'recipient_id' => $user->id,
        ]);

        $response = $this->getJson("/api/v1/notifications/{$notification->id}");

        $response->assertStatus(200);
    }

    public function test_it_returns_user_notifications_with_filters(): void
    {
        $user = User::factory()->create();

        $status = NotificationStatusEnum::ERROR->value;

        Notification::factory(5)->create([
            'recipient_id' => $user->id,
            'status'       => $status,
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}/notifications?status={$status}");

        $response->assertStatus(200);
    }
}
