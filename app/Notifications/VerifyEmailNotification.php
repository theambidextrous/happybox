<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Userinfo;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
    */
    private $jina;
    public function __construct($jina = null)
    {
        $this->jina = $jina;
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
        $verificationUrl = $this->verificationUrl($notifiable);
        $template_ = 'emails.users.verify';
        if( $notifiable->is_partner )
        {
            $template_ = 'emails.partners.verify';
        }
        if( $notifiable->is_partner )
        {
            return (new MailMessage)
                ->view($template_, ['url' => $verificationUrl, 'name' => $notifiable->name, 'email' => $notifiable->email, 'username' => $notifiable->username ])
                ->attach(public_path('guide/2c3cfc5a-2042-11eb-adc1-0242ac120002.pdf'), [
                    'as' => 'partner-guide.pdf',
                    'mime' => 'text/pdf',
                ]);
        }
        else
        {
            return (new MailMessage)->view($template_, ['url' => $verificationUrl, 'name' => $notifiable->name, 'email' => $notifiable->email, 'username' => $notifiable->username ]);
        }
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
