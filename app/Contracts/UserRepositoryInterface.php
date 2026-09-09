<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getNotifications(int $userId, array $data): LengthAwarePaginator;
}
