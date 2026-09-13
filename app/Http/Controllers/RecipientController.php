<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RecipientNotificationsRequest;
use App\Http\Resources\NotificationListResource;
use App\Models\Recipient;
use App\Services\RecipientService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipientController extends Controller
{
    public function getNotifications(
        string $recipientUuid,
        RecipientNotificationsRequest $request,
        RecipientService $service
    ): AnonymousResourceCollection {
        $recipient = Recipient::where('uuid', $recipientUuid)->firstOrFail();
        $data = $request->toDTO()->getDataForFilter();
        $notifications = $service->getNotifications($recipient->id, $data);

        return NotificationListResource::collection($notifications);
    }
}
