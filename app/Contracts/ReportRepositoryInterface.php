<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Report;

interface ReportRepositoryInterface
{
    public function create(array $data): Report;
}
