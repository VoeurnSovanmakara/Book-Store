<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportStuckPurchases extends Command
{
    protected $signature = 'purchases:report-stuck';
    protected $description = 'Log purchases stuck in pending status for over 24 hours';

    public function handle(): int
    {
        $stuck = Purchase::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('No stuck purchases found.');
            return self::SUCCESS;
        }

        Log::warning('Purchases stuck in pending status', [
            'count' => $stuck->count(),
            'purchase_ids' => $stuck->pluck('id')->toArray(),
        ]);

        $this->warn("Found {$stuck->count()} stuck purchases: " . $stuck->pluck('id')->implode(', '));

        return self::SUCCESS;
    }
}