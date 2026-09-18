<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\Recipient;
use Illuminate\Pagination\LengthAwarePaginator;

class RecipientService
{
    public function getNotifications(int $recipientId, array $data): LengthAwarePaginator
    {
        $query = Notification::query()
            ->where('recipient_id', $recipientId);

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

    public function getOrCreate(string $recipientUuid): Recipient
    {
        return Recipient::firstOrCreate(['uuid' => $recipientUuid]);
    }
}
