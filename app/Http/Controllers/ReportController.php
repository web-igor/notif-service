<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ReportStatusEnum;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function createAndGenerateFile(ReportRequest $request, ReportService $service): ReportResource
    {
        $report = $service->createAndGenerateFile($request->validated());
        return ReportResource::make($report);
    }

    public function show(Report $report): ReportResource
    {
        return ReportResource::make($report);
    }

    public function download(Report $report): StreamedResponse
    {
        if (
            $report->status->value !== ReportStatusEnum::COMPLETED->value ||
            is_null($report->file_path)
        ) {
            abort(404, 'Report not ready');
        }

        if (! Storage::disk('local')->exists($report->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($report->file_path);
    }
}
