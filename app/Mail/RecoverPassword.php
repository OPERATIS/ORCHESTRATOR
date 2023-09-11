<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoverPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $subject;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build(): RecoverPassword
    {
        return $this->view('emails.recover-password')
            ->with('token', $this->token);
    }
}
