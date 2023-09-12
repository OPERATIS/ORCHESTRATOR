<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Registration extends Mailable
{
    use Queueable, SerializesModels;

    private $email;
    private $password;

    public $subject = 'Registration';

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function build(): Registration
    {
        return $this->view('emails.registration')
            ->with('email', $this->email)
            ->with('password', $this->password);
    }
}
