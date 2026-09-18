<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Enums\ReportStatusEnum;
use App\Jobs\GenerateReportJob;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Recipient;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Exception;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_report(): void
    {
        $recipient = Recipient::factory()->create();

        $data = [
            'recipient_id' => $recipient->id,
            'from_date'    => '2026-06-01',
            'to_date'      => '2026-09-01',
        ];

        $service = new ReportService();
        $report = $service->create($data);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(ReportStatusEnum::PENDING, $report->status);
        $this->assertEquals($recipient->id, $report->recipient_id);
    }

    public function test_it_creates_and_dispatches_job(): void
    {
        Queue::fake();

        $recipient = Recipient::factory()->create();

        $data = [
            'recipient_uuid' => $recipient->uuid,
            'from_date'      => '2026-06-01',
            'to_date'        => '2026-09-01',
        ];

        $service = new ReportService();
        $report = $service->createAndGenerateFile($data);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(ReportStatusEnum::PENDING, $report->status);

        Queue::assertPushed(GenerateReportJob::class);
    }

    public function test_it_generates_report_file(): void
    {
        Storage::fake('local');

        $recipient = Recipient::factory()->create();
        $client = Client::factory()->create();

        Notification::factory(5)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::EMAIL->value,
            'status'       => NotificationStatusEnum::SENT->value,
            'created_at'   => now(),
        ]);

        Notification::factory(3)->create([
            'recipient_id' => $recipient->id,
            'client_id'    => $client->id,
            'channel'      => ChannelTypeEnum::TELEGRAM->value,
            'status'       => NotificationStatusEnum::ERROR->value,
            'created_at'   => now(),
        ]);

        $report = Report::factory()->create([
            'recipient_id' => $recipient->id,
            'from_date'    => now()->subMonth()->format('Y-m-d'),
            'to_date'      => now()->addMonth()->format('Y-m-d'),
        ]);

        $service = new ReportService();
        $filePath = $service->generateFile($report);

        Storage::disk('local')->assertExists($filePath);

        $content = Storage::disk('local')->get($filePath);

        $this->assertStringContainsString('CHANNELS,TOTAL NOTIFICATIONS,ERRORS', $content);
        $this->assertStringContainsString('email,5,0', $content);
        $this->assertStringContainsString('telegram,3,3', $content);
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
