<?php

declare(strict_types=1);

namespace App\Channels;

use App\Contracts\NotificationChannelInterface;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramChannel implements NotificationChannelInterface
{
    /**
     * @throws RuntimeException
     * @throws Exception
     */
    public function send(string $senderName, string $text, string $address): void
    {
        try {
            $apiDomen = config('services.telegram.api_domen');
            $botToken = config('services.telegram.bot_token');

            $url = "{$apiDomen}{$botToken}";

            Http::post($url . '/sendMessage', [
                'chat_id' => $address,
                'text'    => $text,
            ]);
        } catch (Exception $ex) {
            $message = ! empty($botToken) ?
                str_replace($botToken, '[REDACTED]', $ex->getMessage()) :
                $ex->getMessage();

            throw new RuntimeException(
                "Telegram send failed: {$message}",
                0,
                $ex
            );
        }
    }

    public function getAddress(Notification $notification): ?string
    {
        return $notification->telegram_chat_id;
    }
}
