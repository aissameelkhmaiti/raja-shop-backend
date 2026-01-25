<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use App\Events\RealTimeNotification;

class UserLoginNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public int $userId;
    public string $userName;

    public function __construct(int $userId, string $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Nouvelle connexion',
            'message' => $this->userName . ' vient de se connecter',
            'user_id' => $this->userId,
            'time' => now()->toDateTimeString(),
        ];
    }

    // Cette méthode est appelée automatiquement quand la notification est envoyée
    public function toBroadcast($notifiable)
    {
        return [
            'data' => $this->toArray($notifiable),
            'read_at' => null,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    // Optionnel: Déclencher un événement custom
    public function broadcastOn()
    {
        // Vous pouvez soit diffuser directement ici
        return new \Illuminate\Broadcasting\PrivateChannel('notifications.' . $this->userId);
        
        // OU déclencher l'événement RealTimeNotification
        // event(new RealTimeNotification($this->toArray($notifiable)));
    }
}