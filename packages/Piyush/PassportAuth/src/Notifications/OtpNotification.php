<?php
namespace Piyush\PassportAuth\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class OtpNotification extends Notification
{
    public function __construct(protected string $otp) {}
    public function via($notifiable): array { return ['mail']; }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject('Your verification OTP')->line('Your verification code is:')->line($this->otp)->line('This code expires shortly.');
    }
}
