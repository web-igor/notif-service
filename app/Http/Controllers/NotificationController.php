<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Http\Requests\NotificationUpdateRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function store(NotificationRequest $request, NotificationService $service): NotificationResource
    {
        $notification = $service->createAndSend($request->toDTO());
        return NotificationResource::make($notification);
    }

    public function show(Notification $notification): NotificationResource
    {
        return NotificationResource::make($notification);
    }

    public function updateStatus(Notification $notification, NotificationUpdateRequest $request): NotificationResource
    {
        $notification->update($request->validated());
        return NotificationResource::make($notification);
    }
}
