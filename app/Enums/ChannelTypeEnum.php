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

    public static function fromAbility(AbilitiesEnum $ability): string
    {
        return match ($ability) {
            AbilitiesEnum::SEND_EMAIL    => self::EMAIL->value,
            AbilitiesEnum::SEND_TELEGRAM => self::TELEGRAM->value,
        };
    }
}
