<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuccessfulPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $storeDomain;

    public function __construct($user, $store)
    {
        $this->userName = $user->first_name;
        $this->storeDomain = $store->domain;
    }

    public function build()
    {
        return $this->subject('Successful Payment')
            ->view('emails.successful_payment');
    }
}
