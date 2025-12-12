<?php

namespace App\Console\Commands;

use App\Services\InventorySyncService;
use Illuminate\Console\Command;

class SyncInventoryWithAccurate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-inventory-with-accurate {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory data with Accurate master data';

    protected InventorySyncService $inventorySyncService;

    public function __construct(InventorySyncService $inventorySyncService)
    {
        parent::__construct();
        $this->inventorySyncService = $inventorySyncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Starting inventory sync with Accurate...');

        // Perform the sync using the service
        $results = $this->inventorySyncService->syncWithAccurate($isDryRun);

        // Display results
        foreach ($results['logs'] as $log) {
            $this->line($log);
        }

        // Display errors if any
        if (! empty($results['errors'])) {
            $this->newLine();
            $this->warn('⚠️ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  ❌ {$error}");
            }
        }

        if ($results['success']) {
            $this->newLine();
            $this->info('🎉 Inventory sync completed!');

            return 0;
        } else {
            $this->error('❌ Sync failed!');

            return 1;
        }
    }
}
