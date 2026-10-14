<?php
namespace Piyush\PassportAuth\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class PasswordResetNotification extends Notification
{
    public function __construct(protected string $token) {}
    public function via($notifiable): array { return ['mail']; }
    public function toMail($notifiable): MailMessage
    {
        $base = config('passport-auth.password_reset.url') ?: (config('app.url') . '/' . trim(config('passport-auth.api_prefix', 'api/v1/auth'), '/') . '/password/reset');
        $url = $base . (str_contains($base, '?') ? '&' : '?') . 'token=' . urlencode($this->token) . '&email=' . urlencode($notifiable->email);
        return (new MailMessage)->subject('Reset your password')->line('We received a request to reset your password.')->action('Reset Password', $url)->line('This link expires according to your password reset configuration.');
    }
}
