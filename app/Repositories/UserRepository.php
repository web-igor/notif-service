<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getNotifications(int $userId, array $data): LengthAwarePaginator
    {
        $query = Notification::query()
            ->where('recipient_id', $userId);

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (! empty($data['channel'])) {
            $query->where('channel', $data['channel']);
        }

        $notifications = $query
            ->orderBy($data['sortBy'], $data['sortOrder'])
            ->paginate($data['perPage']);

        return $notifications;
    }
}
