<?php

declare(strict_types=1);

namespace App\Channels;

use App\Contracts\NotificationChannelInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;

class TelegramChannel implements NotificationChannelInterface
{
    public function send(string $senderName, string $text, string $address): void
    {
        $apiDomen = config('services.telegram.api_domen');
        $botToken = config('services.telegram.bot_token');

        $url = "{$apiDomen}{$botToken}";

        Http::post($url . '/sendMessage', [
            'chat_id' => $address,
            'text'    => $text,
        ]);
    }

    public function getAddress(Notification $notification): ?string
    {
        return $notification->telegram_chat_id;
    }
}
