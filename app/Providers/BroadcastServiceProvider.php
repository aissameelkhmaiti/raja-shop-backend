<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Enregistre les routes d'authentification (ex: /broadcasting/auth)
        // C'est ce qui permet à React Echo de vérifier si l'utilisateur a le droit
        // d'écouter un canal privé.
        Broadcast::routes([
        'middleware' => ['auth:sanctum'],
          ]);

        // 2. Charge le fichier routes/channels.php
        // C'est dans ce fichier que vous définissez qui peut accéder à quel canal.
        require base_path('routes/channels.php');
    }
}