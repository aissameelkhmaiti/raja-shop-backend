<?php

namespace App\Jobs;

use App\Mail\LoginAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLoginAlertEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $ip,
        public string $device
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new LoginAlertMail($this->ip, $this->device));
    }
}
