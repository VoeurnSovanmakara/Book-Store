<?php

namespace App\Jobs;

use App\Mail\PurchasePaidMail;
use App\Models\Purchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPurchasePaidEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30; 

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
            ->send(new PurchasePaidMail($this->purchase));
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('api_errors')->error('Failed to send purchase paid email', [
            'purchase_id' => $this->purchase->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
