<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailWithCode extends VerifyEmail
{
    use Queueable;

    public function __construct(
        private readonly string $code
    ) {
        //
    }

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verify your CS2 PickLab email')
            ->greeting('Verify your email address')
            ->line('Click the button below to verify your email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If the button does not work, enter this one-time verification code in your account profile:')
            ->line($this->code)
            ->line('This code expires in 30 minutes.')
            ->line('If you did not create an account, you can ignore this email.');
    }
}