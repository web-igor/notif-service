<?php

declare(strict_types=1);

namespace App\Contracts;

interface NotificationChannelInterface
{
    public function send(int $recipientId, string $text, string $address): bool;
}
