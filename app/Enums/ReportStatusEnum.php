<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Ожидает генерации',
            self::PROCESSING => 'Генерируется',
            self::COMPLETED  => 'Готов к скачиванию',
            self::FAILED     => 'Ошибка генерации',
        };
    }
}
