<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;

class TestBroadcast extends Command
{
    protected $signature = 'test:broadcast';
    protected $description = 'Test broadcast connection to Reverb';

    public function handle()
    {
        $payload = [
            'message' => 'Test direct Reverb 🚀',
            'timestamp' => now()->toISOString(),
        ];

        try {
            broadcast(new \App\Events\RealTimeNotification($payload['message'], 10));
            $this->info("✅ Event broadcasté !");
        } catch (\Exception $e) {
            $this->error("❌ Erreur : " . $e->getMessage());
        }
    }
}
