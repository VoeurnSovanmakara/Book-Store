<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchasePaidMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Purchase $purchase) {}

    public function build(): self
    {
        return $this->subject('Payment Confirmed — Order #' . $this->purchase->id)
            ->markdown('emails.purchase-paid', [
                'purchase' => $this->purchase,
            ]);
    }
}
