<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Notification;
use App\Models\Recipient;

final class TestHelper
{
    public static function createTestUserAndNotifications(): Recipient
    {
        $user = Recipient::factory()->create();

        Notification::factory(10)->create([
            'recipient_id' => $user->id,
            'created_at'   => now()->format('Y-m-d'),
        ]);

        return $user;
    }
}
