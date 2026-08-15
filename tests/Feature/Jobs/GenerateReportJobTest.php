<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Enums\ReportStatusEnum;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelper;
use Exception;

class GenerateReportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_report(): void
    {
        Storage::fake('local');

        $user = TestHelper::createTestUserAndNotifications();

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
            'status'       => ReportStatusEnum::PENDING,
        ]);

        $job = new GenerateReportJob($report, new ReportService());
        $job->handle();

        $report->refresh();

        $this->assertEquals(ReportStatusEnum::COMPLETED, $report->status);
        $this->assertNotNull($report->file_path);

        Storage::disk('local')->assertExists($report->file_path);
    }

    public function test_it_handles_failed_generation(): void
    {
        $user = TestHelper::createTestUserAndNotifications();

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
            'status'       => ReportStatusEnum::PENDING,
        ]);

        $job = new GenerateReportJob($report, new ReportService());
        $job->failed(new Exception('Test error'));

        $report->refresh();

        $this->assertEquals(ReportStatusEnum::FAILED, $report->status);
    }
}
