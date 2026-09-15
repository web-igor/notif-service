<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Notification;

interface NotificationChannelInterface
{
    public function send(string $senderName, string $text, string $address): void;

    public function getAddress(Notification $notification): ?string;
}
