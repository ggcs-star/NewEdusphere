<?php

namespace Piyush\PassportAuth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    public function __construct(protected string $token) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $configured = config('passport-auth.email_verification.url');
        $url = $configured
            ? rtrim($configured, '?&') . (str_contains($configured, '?') ? '&' : '?') . 'token=' . urlencode($this->token)
            : url('/' . trim(config('passport-auth.api_prefix', 'api/v1/auth'), '/') . '/email/verify?token=' . urlencode($this->token));

        return (new MailMessage)
            ->subject('Verify your email address')
            ->line('Please verify your email address.')
            ->action('Verify Email', $url);
    }
}
