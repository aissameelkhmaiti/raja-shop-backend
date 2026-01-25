<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\ActivityLog;

class LogUserLogin
{
    public function handle(UserLoggedIn $event): void
    {
        ActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'module' => 'auth',
            'description' => 'Connexion au dashboard',
            'ip_address' => request()->ip(),
            'device' => request()->userAgent(),
        ]);
    }
}
