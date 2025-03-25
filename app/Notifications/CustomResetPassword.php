<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Support\Facades\Log;


class CustomResetPassword extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        try{
            return (new MailMessage)
                ->view('emails.reset_password', [
                    'userName' => $notifiable->first_name,
                    'actionUrl' => $url,
                ])->subject(__('Password Reset'));
        } catch (\Exception $e) {
            // Hiba logolása
            Log::error("Failed to send password reset email to {$notifiable->email}. Error: {$e->getMessage()}");

            activity()
                ->causedBy($notifiable)
                ->withProperties([
                    'email' => $notifiable->email,
                    'user_id' => $notifiable->id,
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toDateTimeString(),
                ])
                ->log('Failed to send password reset email.');
        }

    }
}
