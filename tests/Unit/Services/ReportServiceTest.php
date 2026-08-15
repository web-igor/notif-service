<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ReportStatusEnum;
use App\Jobs\GenerateReportJob;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Exception;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_report_task(): void
    {
        Queue::fake();

        $service = new ReportService();

        $user = User::factory()->create();

        $data = [
            'recipient_id' => $user->id,
            'from_date'    => '2026-06-01',
            'to_date'      => '2026-09-01',
        ];

        $report = $service->createAndGenerateFile($data);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(ReportStatusEnum::PENDING, $report->status);

        Queue::assertPushed(GenerateReportJob::class);
    }

    /**
     * @throws Exception
     */
    public function test_it_generates_report_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        Notification::factory(10)->create([
            'recipient_id' => $user->id,
            'created_at'   => now(),
        ]);

        $report = Report::factory()->create([
            'recipient_id' => $user->id,
        ]);

        $service = new ReportService();
        $filePath = $service->generateFile($report);

        Storage::disk('local')->assertExists($filePath);
    }

    public function test_it_throws_exception_when_no_data(): void
    {
        $this->expectException(Exception::class);

        $report = Report::factory()->create([
            'recipient_id' => 999,
        ]);

        $service = new ReportService();
        $service->generateFile($report);
    }
}
