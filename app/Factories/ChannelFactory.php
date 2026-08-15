<?php

declare(strict_types=1);

namespace App\Factories;

use App\Channels\EmailChannel;
use App\Channels\TelegramChannel;
use App\Contracts\NotificationChannelInterface;
use App\Enums\ChannelTypeEnum;

class ChannelFactory
{
    public static function make(ChannelTypeEnum $channel): NotificationChannelInterface
    {
        return match ($channel) {
            ChannelTypeEnum::EMAIL    => new EmailChannel(),
            ChannelTypeEnum::TELEGRAM => new TelegramChannel(),
        };
    }
}
