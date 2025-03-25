<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FailedPayment extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $storeDomain;

    public $expireDate;

    public function __construct($user, $store, $expireDate)
    {
        $this->userName = $user->first_name;
        $this->storeDomain = $store->domain;
        $this->expireDate = Carbon::parse($expireDate)->format('F j, Y');
    }

    public function build()
    {
        return $this->subject('Failed Payment')
            ->view('emails.failed_payment');
    }
}
