<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quotation; // Impor model
use Carbon\Carbon; // Impor Carbon
use Illuminate\Support\Facades\Log; // Impor Log

class PruneOldDraftQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotations:prune-drafts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete draft quotations older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to prune old draft quotations...');
        Log::info('Scheduled Task: Pruning old draft quotations started.');

        // Hitung tanggal 30 hari yang lalu
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Cari quotation yang memenuhi kriteria
        $quotationsToDelete = Quotation::where('status', 'draft')
                                        ->where('created_at', '<=', $thirtyDaysAgo)
                                        ->get();

        $count = $quotationsToDelete->count();

        if ($count > 0) {
            foreach ($quotationsToDelete as $quotation) {
                $quotation->delete(); // Hapus satu per satu
            }
            $this->info("Successfully deleted {$count} old draft quotations.");
            Log::info("Scheduled Task: Successfully deleted {$count} old draft quotations.");
        } else {
            $this->info('No old draft quotations to delete.');
            Log::info('Scheduled Task: No old draft quotations to delete.');
        }

        return 0;
    }
}