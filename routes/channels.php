<?php

use Illuminate\Support\Facades\Broadcast;

// ⚠️ ATTENTION : Le paramètre doit correspondre EXACTEMENT au nom dans le canal
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
