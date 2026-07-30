<?php

namespace App\Jobs;

use App\Mail\DailySalesReportMail;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $date) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $purchases = Purchase::whereDate('created_at', $this->date)
            ->where('status', 'paid')
            ->get();

        $summary = [
            'date' => $this->date,
            'total_purchases' => $purchases->count(),
            'total_revenue' => $purchases->sum('total_payable'),
        ];

        $admins = User::all();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new DailySalesReportMail($summary));
        }
    }
}
