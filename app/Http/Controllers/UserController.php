<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserNotificationsRequest;
use App\Http\Resources\NotificationListResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function getUserNotifications(
        User $user,
        UserNotificationsRequest $request,
        UserService $service
    ): AnonymousResourceCollection {
        $data = $request->toDTO()->getDataForFilter();
        $notifications = $service->getNotifications($user->id, $data);

        return NotificationListResource::collection($notifications);
    }
}
