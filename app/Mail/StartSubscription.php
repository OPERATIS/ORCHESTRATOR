<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StartSubscription extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public function build(): StartSubscription
    {
        return $this->view('emails.start-subscription');
    }
}
