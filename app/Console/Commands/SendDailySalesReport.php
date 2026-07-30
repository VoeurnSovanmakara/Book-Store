<?php

namespace App\Console\Commands;

use App\Jobs\SendDailySalesReportEmail;
use Illuminate\Console\Command;

class SendDailySalesReport extends Command
{
    protected $signature = 'reports:daily-sales';
    protected $description = "Send yesterday's sales summary to admins";

    public function handle(): int
    {
        SendDailySalesReportEmail::dispatch(now()->subDay()->toDateString());

        $this->info('Daily sales report job dispatched.');

        return self::SUCCESS;
    }
}