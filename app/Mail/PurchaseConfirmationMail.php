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

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Purchase $purchase) { }

    public function build(): self
    {
        return $this->subject('Order Confirmation #' . $this->purchase->id)
            ->markdown('emails.purchase-confirmation', [
                'purchase' => $this->purchase->load('details.book', 'address'),
            ]);
    }
}
