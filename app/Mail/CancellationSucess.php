<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancellationSucess extends Mailable
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
        return $this->subject('New Voucher | Replacement')
            ->view('emails.orders.cancellation_success')
            ->attach(public_path('hh4c16wwv73khin1oh2vasty8lqzuei0/' . $this->payload['evoucher_attachment']), [
                'as' => 'Your_Replacement_Evourcher.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
