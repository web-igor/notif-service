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
}
