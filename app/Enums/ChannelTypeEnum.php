<?php

declare(strict_types=1);

namespace App\Enums;

enum ChannelTypeEnum: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';

    public static function toArray(): array
    {
        return [
            'email'    => self::EMAIL,
            'telegram' => self::TELEGRAM,
        ];
    }

    public static function allAbilities(): array
    {
        return [
            self::EMAIL->value    => 'notifications:send-email',
            self::TELEGRAM->value => 'notifications:send-telegram',
        ];
    }

    public static function toAbility(string $channel): string
    {
        return match ($channel) {
            self::EMAIL->value    => 'notifications:send-email',
            self::TELEGRAM->value => 'notifications:send-telegram',
        };
    }

    public static function fromAbility(string $ability): string
    {
        return match ($ability) {
            'notifications:send-email'    => self::EMAIL->value,
            'notifications:send-telegram' => self::TELEGRAM->value,
        };
    }
}
