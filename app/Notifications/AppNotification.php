<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use Illuminate\Bus\Queueable;
//  COMMENTEZ CETTE LIGNE
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

//  RETIREZ "implements ShouldQueue"
class AppNotification extends Notification
{
    use Queueable;

    public function __construct(
        public NotificationType $type,
        public array $data
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type->value,
            'data' => $this->data,
            'created_at' => now(),
        ];
    }
}