<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ItemLabelsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('item_labels')->truncate();
    DB::table('item_labels')->insert([
      [
        'goods_receipt_item_id' => 1,
        'item_code' => 'RM-001',
        'item_name' => 'Besi Batangan',
        'quantity' => 100,
        'qr_code' => Str::uuid()->toString(),
        'batch_no' => 'BATCH-2025-001',
        'location_id' => 1,
        'rack_id' => 1,
        'pallet_id' => 1,
        'status' => 'stored',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'goods_receipt_item_id' => 1,
        'item_code' => 'RM-002',
        'item_name' => 'Plastik Granul',
        'quantity' => 200,
        'qr_code' => Str::uuid()->toString(),
        'batch_no' => 'BATCH-2025-002',
        'location_id' => 1,
        'rack_id' => 1,
        'pallet_id' => 2,
        'status' => 'stored',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'goods_receipt_item_id' => 2,
        'item_code' => 'FG-001',
        'item_name' => 'Botol Air Mineral 600ml',
        'quantity' => 500,
        'qr_code' => Str::uuid()->toString(),
        'batch_no' => 'BATCH-2025-003',
        'location_id' => 2,
        'rack_id' => 2,
        'pallet_id' => 3,
        'status' => 'stored',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
