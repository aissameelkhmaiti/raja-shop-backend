<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\User;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminUserLogin
{
    public function handle(UserLoggedIn $event): void
    {
        $admins = User::where('role', 'admin')->get();

        Notification::send(
            $admins,
            new UserLoginNotification(
                $event->user->id,
                $event->user->name
            )
        );
    }
}
