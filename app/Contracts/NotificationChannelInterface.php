<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Notification;

interface NotificationChannelInterface
{
    public function send(int $recipientId, string $text, string $address): bool;

    public function getAddress(Notification $notification): ?string;
}
