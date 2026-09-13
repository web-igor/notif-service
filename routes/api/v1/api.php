<?php

declare(strict_types=1);

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\Client\ClientController;

Route::name('api.v1.')
    ->prefix('v1')
    ->group(function () {
        Route::post('/register', [ClientController::class, 'register'])
            ->name('register');
        Route::post('/login', [ClientController::class, 'login'])
            ->name('login');

        Route::middleware('auth:client')->group(function () {
            Route::get('/tokens', [ClientController::class, 'tokens'])
                ->name('tokens');
            Route::get('/revoke-token', [ClientController::class, 'revokeToken'])
                ->name('revoke-token');
            Route::get('/revoke-all-tokens', [ClientController::class, 'revokeAllTokens'])
                ->name('revoke-all-tokens');

            Route::get('/recipients/{recipientUuid}/notifications', [RecipientController::class, 'getNotifications'])
                ->name('recipients.getNotifications');

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
    });
