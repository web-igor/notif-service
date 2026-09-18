<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\NotificationData;
use App\Jobs\SendNotificationJob;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Token;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class NotificationService
{
    /**
     * @throws AccessDeniedHttpException
     * @throws Exception
     */
    public function createAndSend(NotificationData $dto, Client $client): Notification
    {
        DB::beginTransaction();
        try {
            $recipientService = app(RecipientService::class);
            $data = $dto->getDataForCreate();
            $recipient = $recipientService->getOrCreate($data['recipientUuid']);
            $data['recipient_id'] = $recipient->id;
            $data['client_id'] = $client->id;

            $notification = $this->create($data);

            DB::commit();

            $this->tokenRightsCheck($notification);
            SendNotificationJob::dispatch($notification)->onQueue('notifications');

            return $notification;

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @throws AccessDeniedHttpException
     */
    private function tokenRightsCheck(Notification $notification): void
    {
        /**
         * @var Client $client
         */
        $client = request()->user();
        $token = $client->currentAccessToken();

        if (! $token instanceof Token) {
            throw new AccessDeniedHttpException('The token must belong to the Token class.');
        }

        if (! in_array($notification->channel->value, $token->channels, true)) {
            throw new AccessDeniedHttpException(
                'Token does not have permission to send via this channel.'
            );
        }
    }

    private function create(array $data): Notification
    {
        return Notification::create($data);
    }
}
