<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AbilitiesEnum;
use App\Enums\ChannelTypeEnum;
use App\Models\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

final readonly class ClientService
{
    /**
     * @param  array<string, mixed>  $data
     *
     * @throws ModelNotFoundException
     * @throws AuthenticationException
     */
    public function login(array $data): string
    {
        $client = Client::where('email', $data['email'])->first();

        if (! $client) {
            throw new ModelNotFoundException('Client not found');
        }

        if (! Hash::check($data['password'], $client->password)) {
            throw new AuthenticationException('Invalid password');
        }

        $tokenName = 'token_' . bin2hex(random_bytes(16));

        if (empty($data['channels'])) {
            $abilities = array_values(AbilitiesEnum::allAbilities());

            $token = $client->createToken($tokenName, $abilities)->plainTextToken;
        } else {
            $abilities = array_map(function ($item) {
                $ability = ChannelTypeEnum::from($item);
                return AbilitiesEnum::toAbility($ability);
            }, $data['channels']);

            $token = $client->createToken($tokenName, $abilities)->plainTextToken;
        }

        return $token;
    }
}
