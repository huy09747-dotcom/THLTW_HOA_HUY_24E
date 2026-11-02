<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordLink extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token)
    {
        $this->onConnection('database');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = rtrim(config('payments.frontend'), '/').'/reset-password/'
            .rawurlencode($this->token).'?email='.rawurlencode($notifiable->email);
        return (new MailMessage)
            ->subject('HH Shop - Đặt lại mật khẩu')
            ->view('emails.reset-password', [
                'name' => $notifiable->name,
                'url' => $url,
                'minutes' => config('auth.passwords.users.expire'),
            ]);
    }
}
