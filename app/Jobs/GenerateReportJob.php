<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use App\Services\ReportService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60];

    public function __construct(
        private readonly Report $report,
        private readonly ReportService $service,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $report = $this->report;

        if ($report->status === ReportStatusEnum::COMPLETED) {
            return;
        }

        $report->update(['status' => ReportStatusEnum::PROCESSING]);

        try {
            $filePath = $this->service->generateFile($this->report);

            if (! $filePath) {
                throw new Exception('Failed to save file.');
            }

            $this->report->update(
                [
                    'status'    => ReportStatusEnum::COMPLETED,
                    'file_path' => $filePath,
                ]
            );
        } catch (Throwable $ex) {
            Log::error('GenerateReportJob failed: ' . $ex->getMessage(), [
                'report_id' => $this->report->id,
            ]);

            throw $ex;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->report->update(['status' => ReportStatusEnum::FAILED]);
    }
}
