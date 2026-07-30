<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailySalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $summary) {}

    public function build(): self
    {
        return $this->subject('Daily Sales Report —' . $this->summary['date'])
            ->markdown('emails.daily-sales-report', [
                'summary' => $this->summary,
            ]);
    }
}
