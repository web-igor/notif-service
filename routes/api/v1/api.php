<?php

declare(strict_types=1);

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

Route::name('api.v1.')
    ->prefix('v1')
    ->group(function () {
        Route::get('/users/{user}/notifications', [UserController::class, 'getUserNotifications'])
            ->name('user.getUserNotifications');

        Route::post('/notifications', [NotificationController::class, 'store'])
            ->name('notifications.store');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
            ->name('notifications.show');

        Route::post('/reports', [ReportController::class, 'createAndGenerateFile'])
            ->name('reports.createAndGenerateFile');
        Route::get('/reports/{report}', [ReportController::class, 'show'])
            ->name('reports.show');
        Route::get('/reports/{report}/download', [ReportController::class, 'download'])
            ->name('reports.download');
    });
