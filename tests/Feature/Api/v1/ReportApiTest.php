<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use App\Enums\ReportStatusEnum;
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
        $user = TestHelper::createTestUserAndNotifications();

        $response = $this->postJson('/api/v1/reports', [
            'recipient_id' => $user->id,
            'from_date'    => now()->subDay()->format('Y-m-d'),
            'to_date'      => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertStatus(201);
    }

    public function test_it_returns_report_status(): void
    {
        $user = TestHelper::createTestUserAndNotifications();

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
            'from_date'    => now()->subDay()->format('Y-m-d'),
            'to_date'      => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->getJson("/api/v1/reports/{$report->id}");

        $response->assertStatus(200);
    }

    public function test_it_downloads_report_file(): void
    {
        $user = TestHelper::createTestUserAndNotifications();

        Storage::fake('local');

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
            'status'       => ReportStatusEnum::COMPLETED,
            'file_path'    => 'reports/test.csv',
        ]);

        Storage::disk('local')->put('reports/test.csv', 'test content');

        $response = $this->getJson("/api/v1/reports/{$report->id}/download");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    public function test_it_returns_404_for_not_ready_report(): void
    {
        $user = TestHelper::createTestUserAndNotifications();

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
            'status'       => ReportStatusEnum::PENDING,
        ]);

        $response = $this->getJson("/api/v1/reports/{$report->id}/download");

        $response->assertStatus(404);
    }
}
