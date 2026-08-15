<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ChannelTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Enums\ReportStatusEnum;
use App\Jobs\GenerateReportJob;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Exception;

readonly class ReportService
{
    public function create(array $data): Report
    {
        return Report::create(
            array_merge($data, ['status' => ReportStatusEnum::PENDING])
        );
    }

    public function createAndGenerateFile(array $data)
    {
        $report = $this->create($data);
        GenerateReportJob::dispatch($report, $this)->onQueue('reports');

        return $report;
    }

    /**
     * @throws Exception
     */
    public function generateFile(Report $report): ?string
    {
        $filePath = $this->getFilePath($report);
        $statistics = $this->getStatistics($report);

        if ($statistics->isEmpty()) {
            throw new Exception('No statistics found for this report.');
        }

        $content = "CHANNELS,TOTAL NOTIFICATIONS,ERRORS\n";

        foreach ($statistics as $row) {
            $content .= "{$row->channel->value},{$row->total_notifications},{$row->errors}\n";
        }

        $savedFile = Storage::disk('local')->put($filePath, $content);

        if (! $savedFile) {
            throw new Exception('Failed to save file: ' . $filePath);
        }

        return $filePath;
    }

    private function getFilePath(Report $report): string
    {
        $fileName = sprintf(
            'report_%d_%s_%s.csv',
            $report->recipient_id,
            $report->from_date,
            $report->to_date
        );

        return 'reports/' . $fileName;
    }

    /**
     * @return Collection<object{channel: ChannelTypeEnum, total_notifications: int, errors: int}>
     */
    private function getStatistics(Report $report): Collection
    {
        return Notification::query()
            ->selectRaw(
                '
                channel,
                COUNT(*) AS total_notifications,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS errors
                ',
                [NotificationStatusEnum::ERROR->value]
            )
            ->where('recipient_id', $report->recipient_id)
            ->whereBetween('created_at', [$report->from_date, $report->to_date])
            ->groupBy('channel')
            ->get();
    }
}
