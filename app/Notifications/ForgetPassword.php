<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPassword extends Notification
{
    use Queueable;
    public $message;
    public $fromMail;
    public $subject;
    public $mailer;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message= "You forget your password";
        $this->subject="OTP varification";
        $this->fromMail="mhdphp2022@gmail.com";
        $this->mailer="smtp";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->mailer('smtp')
                    ->subject($this->subject)
                    ->greeting("hello ". $notifiable->first_name)
                    ->line($this->message);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
