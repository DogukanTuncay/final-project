<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    protected $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('E-posta Adresinizi Doğrulayın')
            ->line('Hesabınızı doğrulamak için aşağıdaki butona tıklayın.')
            ->action('E-posta Adresimi Doğrula', $this->verificationUrl)
            ->line('Eğer bir hesap oluşturmadıysanız, başka bir işlem yapmanıza gerek yoktur.');
    }
}
