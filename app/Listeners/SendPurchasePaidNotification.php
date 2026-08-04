<?php

namespace App\Listeners;

use App\Events\PurchasePaid;
use App\Jobs\SendPurchasePaidEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPurchasePaidNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PurchasePaid $event): void
    {
        SendPurchasePaidEmail::dispatch($event->purchase);
    }
}
