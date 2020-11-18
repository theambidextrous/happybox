<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EboxDelivery extends Mailable
{
    use Queueable, SerializesModels;
    public $payload;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Gift | HappyBox For You')
            ->view('emails.orders.eboxdelivery')
            ->attach(public_path('media/' . $this->payload['ebook_attachment']), [
                'as' => 'Your_Ebook.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach(public_path('hh4c16wwv73khin1oh2vasty8lqzuei0/' . $this->payload['evoucher_attachment']), [
                'as' => 'Your_Evourcher.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
