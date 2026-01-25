<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class LoginAlertMail extends Mailable
{
    public function __construct(
        public string $ip,
        public string $device
    ) {}

    public function build()
    {
        return $this
            ->subject('Nouvelle connexion à votre compte')
            ->view('emails.login-alert'); // Crée ce fichier blade
    }
}
