<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($twoFactorCode)
    {
        $this->twoFactorCode = $twoFactorCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function build()
    {
        return $this->subject('Your Two-Factor Authentication Code')
            ->view('emails.two_factor_code');
    }

}
