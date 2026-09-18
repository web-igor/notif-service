<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Enums\ReportStatusEnum;
use App\Models\Notification;
use App\Models\Recipient;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelper;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_report_request(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();
        $recipient = Recipient::factory()->create();

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::SENT->value,
            'created_at'   => now(),
        ]);

        $response = $this->withToken($token)
            ->postJson('/api/v1/reports', [
                'recipient_uuid' => $recipient->uuid,
                'from_date'      => now()->subDay()->format('Y-m-d'),
                'to_date'        => now()->addDay()->format('Y-m-d'),
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'status' => ReportStatusEnum::PENDING->value,
            ],
        ]);
    }

    public function test_it_returns_report(): void
    {
        [$client, $token] = TestHelper::createClientAndAuthToken();
        $recipient = Recipient::factory()->create();

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::SENT->value,
            'created_at'   => now(),
        ]);

        $report = Report::factory()->create([
            'recipient_id' => $recipient->id,
            'from_date'    => now()->subMonth()->format('Y-m-d'),
            'to_date'      => now()->addMonth()->format('Y-m-d'),
        ]);

        $response = $this->withToken($token)
            ->getJson("/api/v1/reports/{$report->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'status']]);
        $response->assertJsonPath('data.id', $report->id);
        $this->assertContains(
            $response->json('data.status'),
            array_column(ReportStatusEnum::cases(), 'value')
        );
    }

    public function test_it_downloads_report_file(): void
    {
        Storage::fake('local');

        [$client, $token] = TestHelper::createClientAndAuthToken();

        $recipient = Recipient::factory()->create();

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::SENT->value,
            'created_at'   => now(),
        ]);

        $report = Report::factory()->create([
            'recipient_id' => $recipient->id,
            'status'       => ReportStatusEnum::COMPLETED,
            'file_path'    => 'reports/test.csv',
        ]);

        Storage::disk('local')->put('reports/test.csv', 'test content');

        $response = $this->withToken($token)
            ->getJson("/api/v1/reports/{$report->id}/download");

        $response->assertStatus(200)
            ->assertHeader('Content-Disposition')
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
    }
}
