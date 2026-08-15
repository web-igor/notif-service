<?php

declare(strict_types=1);

namespace App\Channels;

use App\Contracts\NotificationChannelInterface;
use Log;

class TelegramChannel implements NotificationChannelInterface
{
    public function send(int $recipientId, string $text, string $address): bool
    {
        /**
         * Заглушка. В реальности тут должна быть отправка уведомления в Telegram
         */
        Log::info("Telegram sent to {$address}: {$text}");
        return true;
    }
}
