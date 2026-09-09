<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class UserService
{
    public function getNotifications(
        int $userId,
        array $data,
        UserRepositoryInterface $userRepository
    ): LengthAwarePaginator {
        return $userRepository->getNotifications($userId, $data);
    }
}
