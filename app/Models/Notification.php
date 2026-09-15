<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ChannelTypeEnum $channel
 * @property NotificationStatusEnum $status
 */
class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected function casts(): array
    {
        return [
            'channel' => ChannelTypeEnum::class,
            'status'  => NotificationStatusEnum::class,
        ];
    }
}
