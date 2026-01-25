<?php

namespace App\Listeners;

use App\Events\AppNotificationEvent;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Enums\NotificationType;

class SendAppNotification
{
    public function handle(AppNotificationEvent $event): void
    {
        $data = $event->notification;

        $user = User::find($data['user_id']);

        if (! $user) {
            return;
        }

        //  SEULE ACTION QUI ÉCRIT EN DB
        $user->notify(
            new AppNotification(
                NotificationType::from($data['type']),
                $data
            )
        );
    }
}
