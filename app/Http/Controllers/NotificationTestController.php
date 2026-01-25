<?php

namespace App\Http\Controllers;

use App\Events\RealTimeNotification;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
    /**
     * Déclencher une notification pour l'utilisateur donné.
     */
    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $userId = $request->input('user_id');
        $message = $request->input('message');

        event(new RealTimeNotification($message, $userId));

        return response()->json([
            'success' => true,
            'message' => 'Notification envoyée en temps réel !',
        ]);
    }
}
