<?php

declare(strict_types=1);

namespace App\Channels;

use App\Contracts\NotificationChannelInterface;
use App\Mail\Email;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements NotificationChannelInterface
{
    public function send(string $senderName, string $text, string $address): void
    {
        Mail::to([$address])->send(new Email($senderName, $text));
    }

    public function getAddress(Notification $notification): ?string
    {
        return $notification->email;
    }
}
