<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class ClientTokenApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_client(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name'     => 'Test Client',
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Client Test Client created successfully',
        ]);

        $this->assertDatabaseHas('clients', [
            'name'  => 'Test Client',
            'email' => 'test@example.com',
        ]);
    }

    public function test_it_logins_client(): void
    {
        $this->postJson('/api/v1/register', [
            'name'     => 'Test Client',
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'access_token',
        ]);
    }

    public function test_it_returns_tokens(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $response = $this->withToken($token)
            ->getJson('/api/v1/tokens');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'channels'],
            ],
        ]);
    }

    public function test_it_revokes_token(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $response = $this->withToken($token)
            ->getJson('/api/v1/revoke-token');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'test-token',
        ]);
    }

    public function test_it_revokes_all_tokens(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();

        $response = $this->withToken($token)
            ->getJson('/api/v1/revoke-all-tokens');

        $response->assertStatus(200);
        $response->assertJson([
            'success'       => true,
            'revoked_count' => 1,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'test-token',
        ]);
    }
}
