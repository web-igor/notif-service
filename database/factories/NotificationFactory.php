<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $channels = array_column(ChannelTypeEnum::cases(), 'value');
        $statuses = array_column(NotificationStatusEnum::cases(), 'value');

        return [
            'channel' => fake()->randomElement($channels),
            'status'  => fake()->randomElement($statuses),
            'text'    => fake()->text(500),
        ];
    }
}
