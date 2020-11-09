<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
class PasswordResetNotification extends Notification
{
    use Queueable;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $reset_link = config('app.client_url') . 'reset-password.php?token=' . $this->token;
        $template_ = 'emails.users.forgot';
        if( $notifiable->is_partner )
        {
            $template_ = 'emails.partners.forgot';
        }
        return (new MailMessage)->view($template_, ['url'=> $reset_link, 'name'=> $notifiable->name ]);

        // return (new MailMessage)
        //     ->subject('Reset your HappyBox Account Password.')
        //     ->line('You are receiving this email because we received a password reset request for your account.')
        //     ->action('Reset Password', $notification_link)
        //     ->line('This reset link will expire in '.config('auth.passwords.users.expire').' minutes.')
        //     ->line('If you did not make this request, no action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
