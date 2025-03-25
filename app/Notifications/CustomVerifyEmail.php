<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Create the email verification notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        try {

            return (new MailMessage)
                ->view('emails.verify_email', [
                    'verificationUrl' => $verificationUrl,
                    'userName' => $notifiable->first_name,
                ])
                ->subject(__('Verify Your Email Address'));

        } catch (\Exception $e) {
            // Hiba logolása
            Log::error("Failed to send email verification to {$notifiable->email}. Error: {$e->getMessage()}");

            activity()
                ->causedBy($notifiable)
                ->withProperties([
                    'email' => $notifiable->email,
                    'user_id' => $notifiable->id,
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toDateTimeString(),
                ])
                ->log('Failed to send email verification.');
        }
    }
}
