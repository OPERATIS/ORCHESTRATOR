<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EndSubscription extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public function build(): EndSubscription
    {
        return $this->view('emails.end-subscription');
    }
}
