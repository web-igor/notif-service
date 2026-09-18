<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\AbilitiesEnum;
use App\Models\Client;

final class TestHelper
{
    public static function createClientAndAuthToken(): array
    {
        $client = Client::factory()->create();

        $token = $client->createToken(
            'test-token',
            [AbilitiesEnum::SEND_EMAIL->value]
        );

        return [$client, $token->plainTextToken];
    }
}
