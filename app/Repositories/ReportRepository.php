<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\ReportRepositoryInterface;
use App\Enums\ReportStatusEnum;
use App\Models\Report;

class ReportRepository implements ReportRepositoryInterface
{
    public function create(array $data): Report
    {
        return Report::create(
            array_merge($data, ['status' => ReportStatusEnum::PENDING])
        );
    }
}
