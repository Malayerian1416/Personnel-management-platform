<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('همیاران شمال شرق - بازنشانی کلمه عبور')
            ->line('شما این ایمیل را دریافت می کنید زیرا ما یک درخواست بازنشانی رمز عبور برای حساب شما دریافت کردیم.') // Here are the lines you can safely override
            ->action('بازنشانی کلمه عبور', url('password/reset', $this->token))
            ->line('اگر بازنشانی رمز عبور را درخواست نکردید، هیچ اقدام دیگری لازم نیست.')
            ->greeting('درخواست بازنشانی کلمه عبور');
    }
}
