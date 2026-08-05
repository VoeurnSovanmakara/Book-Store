<?php

namespace App\Jobs;

use App\Mail\PurchaseConfirmationMail;
use App\Models\Purchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPurchaseConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30; // seconds between retries

    /**
     * Create a new job instance.
     */
    public function __construct(public Purchase $purchase) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->purchase->customer->email)
            ->send(new PurchaseConfirmationMail($this->purchase));
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('api_errors')->error('Failed to send purchase confirmation email', [
            'purchase_id' => $this->purchase->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
