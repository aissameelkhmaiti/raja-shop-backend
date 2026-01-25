<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppNotificationEvent
{
    use Dispatchable, SerializesModels;

    public array $notification;

    public function __construct(array $notification)
    {
        $this->notification = $notification;
    }
}
