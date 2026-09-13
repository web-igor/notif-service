<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AbilitiesEnum;
use App\Enums\ChannelTypeEnum;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property array $channels
 */
class Token extends PersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    public function getChannelsAttribute(): array
    {
        return array_map(
            fn (string $ability) => ChannelTypeEnum::fromAbility(
                AbilitiesEnum::from($ability)
            ),
            $this->abilities,
        );
    }
}
