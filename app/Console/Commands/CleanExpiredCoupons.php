<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredCoupons extends Command
{
    protected $signature = 'coupons:clean-expired';
    protected $description = 'Remove coupons that expired more than 30 days ago';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $count = Coupon::where('expired_date', '<', $cutoff)->count();

        Coupon::where('expired_date', '<', $cutoff)->delete();

        $this->info("Deleted {$count} expired coupons.");

        Log::info('Scheduled task: expired coupons cleaned', ['count' => $count]);

        return self::SUCCESS;
    }
}