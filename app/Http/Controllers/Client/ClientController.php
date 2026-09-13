<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use App\Http\Resources\TokenResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Client {$client->name} created successfully",
        ]);
    }

    /**
     * @throws ModelNotFoundException
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request, ClientService $service): JsonResponse
    {
        $token = $service->login($request->validated());

        return response()->json([
            'success'      => true,
            'access_token' => $token,
        ]);
    }

    public function tokens(): AnonymousResourceCollection
    {
        /**
         * @var Client $client
         */
        $client = request()->user();
        $tokens = $client->tokens()->get();

        return TokenResource::collection($tokens);
    }

    public function revokeToken(): JsonResponse
    {
        /**
         * @var Client $client
         */
        $client = request()->user();
        $token = $client->currentAccessToken();

        return response()->json(['success' => $token->delete()]);
    }

    public function revokeAllTokens(): JsonResponse
    {
        /**
         * @var Client $client
         */
        $client = request()->user();
        $tokens = $client->tokens();

        return response()->json([
            'success'       => true,
            'revoked_count' => $tokens->delete(),
        ]);
    }
}
