<?php

declare(strict_types=1);

namespace App\Enums;

enum AbilitiesEnum: string
{
    case SEND_EMAIL = 'notifications:send-email';
    case SEND_TELEGRAM = 'notifications:send-telegram';

    public static function allAbilities(): array
    {
        return [
            ChannelTypeEnum::EMAIL->value    => self::SEND_EMAIL->value,
            ChannelTypeEnum::TELEGRAM->value => self::SEND_TELEGRAM->value,
        ];
    }

    public static function fromChannel(ChannelTypeEnum $channel): string
    {
        return match ($channel) {
            ChannelTypeEnum::EMAIL    => self::SEND_EMAIL->value,
            ChannelTypeEnum::TELEGRAM => self::SEND_TELEGRAM->value,
        };
    }
}
