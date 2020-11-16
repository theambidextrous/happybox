<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Order;

class OrderPaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Payment Acknowledgement')
            ->view('emails.orders.paymentreceived');
            // ->attach(public_path('hh4c16wwv73khin1oh2vasty8lqzuei0/' . $this->order['invoice_attachment']), [
            //     'as' => 'Your_Invoice.pdf',
            //     'mime' => 'application/pdf',
            // ]);
    }
}
