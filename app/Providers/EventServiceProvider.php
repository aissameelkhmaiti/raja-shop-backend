<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

  protected $listen = [
        \App\Events\UserLoggedIn::class => [
            \App\Listeners\LogUserLogin::class,
            \App\Listeners\NotifyAdminUserLogin::class,
        ],

        \App\Events\AppNotificationEvent::class => [
            \App\Listeners\SendAppNotification::class,
        ],
    ];
    
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
