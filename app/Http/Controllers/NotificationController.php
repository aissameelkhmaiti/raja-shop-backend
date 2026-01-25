<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Récupérer toutes les notifications
     * - Admin : toutes les notifications
     * - Customer : seulement les siennes
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Admin : toutes les notifications
            $notifications = DB::table('notifications')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'type' => $notif->type,
                        'data' => json_decode($notif->data, true),
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at,
                        'updated_at' => $notif->updated_at,
                        'notifiable_id' => $notif->notifiable_id,
                        'notifiable_type' => $notif->notifiable_type,
                    ];
                });
        } else {
            // Customer : seulement ses notifications
            $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        }

        return response()->json($notifications);
    }

    /**
     * Notifications non lues
     */
    public function unread(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Admin : toutes les notifications non lues
            $notifications = DB::table('notifications')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'type' => $notif->type,
                        'data' => json_decode($notif->data, true),
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at,
                        'updated_at' => $notif->updated_at,
                        'notifiable_id' => $notif->notifiable_id,
                        'notifiable_type' => $notif->notifiable_type,
                    ];
                });
        } else {
            // Customer : seulement ses notifications non lues
            $notifications = $user->unreadNotifications;
        }

        return response()->json($notifications);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);
        } else {
            $notification = $user->notifications()->findOrFail($id);
            $notification->markAsRead();
        }

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            DB::table('notifications')->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['message' => 'Toutes les notifications sont marquées comme lues']);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            DB::table('notifications')->where('id', $id)->delete();
        } else {
            $notification = $user->notifications()->findOrFail($id);
            $notification->delete();
        }

        return response()->json(['message' => 'Notification supprimée']);
    }
}
