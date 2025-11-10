<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Inventory\Inventory;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;

class InventoriesSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::all();
        $locations = Location::all();

        if ($items->count() < 1) {
            $this->command->warn('⚠️ Tidak ada item ditemukan. Seeder dibatalkan.');
            return;
        }

        // Hapus semua data lama agar tidak duplikat
        Inventory::truncate();

        foreach ($items as $item) {
            Inventory::factory()->create([
                'item_id' => $item->id,
                'location_id' => $locations->random()->id ?? Location::factory()->create()->id,
            ]);
        }

        $this->command->info('✅ Inventories berhasil di-seed (1 per item, unique item_id).');
    }
}
