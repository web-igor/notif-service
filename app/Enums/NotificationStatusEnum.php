<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case ERROR = 'error';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'В обработке',
            self::SENT    => 'Отправлено',
            self::ERROR   => 'Ошибка',
        };
    }
}
