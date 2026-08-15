<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Queue\Queueable;

/**
 * @property ChannelTypeEnum $channel
 * @property NotificationStatusEnum $status
 */
class Notification extends Model
{
    use HasFactory;
    use Queueable;

    protected $table = 'notifications';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'channel' => ChannelTypeEnum::class,
            'status'  => NotificationStatusEnum::class,
        ];
    }
}
