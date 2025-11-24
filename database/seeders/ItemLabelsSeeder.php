<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemLabelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Handle truncation based on database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('item_labels')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use TRUNCATE CASCADE to handle foreign key constraints
            DB::statement('TRUNCATE TABLE item_labels RESTART IDENTITY CASCADE;');
        } else {
            DB::table('item_labels')->truncate();
        }

        /**
         * Catatan: Disesuaikan dengan ItemsSeeder
         * Item Labels menggunakan item_code dari ItemsSeeder
         * Raw Material items dengan location_id 1 dan 2
         */
        DB::table('item_labels')->insert([
            // --- RAW MATERIAL - LOCATION 1 (Gudang Utama) ---
            [
                'item_code' => '100009',
                'item_name' => 'Polybutylene',
                'goods_receipt_item_id' => 1,
                'qr_code' => 'QR-100009-B001',
                'batch_no' => 'B001-20231101',
                'location_id' => 1,
                'rack_id' => 1,
                'pallet_id' => 1,
                'quantity' => 500, // KG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100008',
                'item_name' => 'Polycarbonat',
                'goods_receipt_item_id' => 2,
                'qr_code' => 'QR-100008-B002',
                'batch_no' => 'B002-20231102',
                'location_id' => 1,
                'rack_id' => 2,
                'pallet_id' => 2,
                'quantity' => 750, // KG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100007',
                'item_name' => 'Polypropylene',
                'goods_receipt_item_id' => 3,
                'qr_code' => 'QR-100007-B003',
                'batch_no' => 'B003-20231103',
                'location_id' => 1,
                'rack_id' => 3,
                'pallet_id' => 3,
                'quantity' => 1000, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100025',
                'item_name' => 'Hard Coating',
                'goods_receipt_item_id' => 4,
                'qr_code' => 'QR-100025-B004',
                'batch_no' => 'B004-20231104',
                'location_id' => 1,
                'rack_id' => 4,
                'pallet_id' => 4,
                'quantity' => 200, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100026',
                'item_name' => 'Basecoat',
                'goods_receipt_item_id' => 5,
                'qr_code' => 'QR-100026-B005',
                'batch_no' => 'B005-20231105',
                'location_id' => 1,
                'rack_id' => 5,
                'pallet_id' => 5,
                'quantity' => 150, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100024',
                'item_name' => 'Cat',
                'goods_receipt_item_id' => 6,
                'qr_code' => 'QR-100024-B006',
                'batch_no' => 'B006-20231106',
                'location_id' => 1,
                'rack_id' => 6,
                'pallet_id' => 6,
                'quantity' => 300, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100010',
                'item_name' => 'Harness',
                'goods_receipt_item_id' => 7,
                'qr_code' => 'QR-100010-B007',
                'batch_no' => 'B007-20231107',
                'location_id' => 1,
                'rack_id' => 7,
                'pallet_id' => 7,
                'quantity' => 500, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100011',
                'item_name' => 'Screw',
                'goods_receipt_item_id' => 8,
                'qr_code' => 'QR-100011-B008',
                'batch_no' => 'B008-20231108',
                'location_id' => 1,
                'rack_id' => 8,
                'pallet_id' => 8,
                'quantity' => 5000, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100012',
                'item_name' => 'Actuator',
                'goods_receipt_item_id' => 9,
                'qr_code' => 'QR-100012-B009',
                'batch_no' => 'B009-20231109',
                'location_id' => 1,
                'rack_id' => 9,
                'pallet_id' => 9,
                'quantity' => 100, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- RAW MATERIAL - LOCATION 2 (Gudang Khusus) ---
            [
                'item_code' => '100020',
                'item_name' => 'Adjuster',
                'goods_receipt_item_id' => 10,
                'qr_code' => 'QR-100020-B010',
                'batch_no' => 'B010-20231110',
                'location_id' => 2,
                'rack_id' => 10,
                'pallet_id' => 10,
                'quantity' => 250, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100013',
                'item_name' => 'Cover',
                'goods_receipt_item_id' => 11,
                'qr_code' => 'QR-100013-B011',
                'batch_no' => 'B011-20231111',
                'location_id' => 2,
                'rack_id' => 11,
                'pallet_id' => 11,
                'quantity' => 400, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100019',
                'item_name' => 'Grommet',
                'goods_receipt_item_id' => 12,
                'qr_code' => 'QR-100019-B012',
                'batch_no' => 'B012-20231112',
                'location_id' => 2,
                'rack_id' => 12,
                'pallet_id' => 12,
                'quantity' => 600, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100027',
                'item_name' => 'Spring',
                'goods_receipt_item_id' => 13,
                'qr_code' => 'QR-100027-B013',
                'batch_no' => 'B013-20231113',
                'location_id' => 2,
                'rack_id' => 13,
                'pallet_id' => 13,
                'quantity' => 800, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100014',
                'item_name' => 'Socket',
                'goods_receipt_item_id' => 14,
                'qr_code' => 'QR-100014-B014',
                'batch_no' => 'B014-20231114',
                'location_id' => 2,
                'rack_id' => 14,
                'pallet_id' => 14,
                'quantity' => 300, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100015',
                'item_name' => 'Bulb Halogen (H4)',
                'goods_receipt_item_id' => 15,
                'qr_code' => 'QR-100015-B015',
                'batch_no' => 'B015-20231115',
                'location_id' => 2,
                'rack_id' => 15,
                'pallet_id' => 15,
                'quantity' => 200, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100016',
                'item_name' => 'Bulb Umber',
                'goods_receipt_item_id' => 16,
                'qr_code' => 'QR-100016-B016',
                'batch_no' => 'B016-20231116',
                'location_id' => 2,
                'rack_id' => 16,
                'pallet_id' => 16,
                'quantity' => 350, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100017',
                'item_name' => 'Bulb Senja (T10)',
                'goods_receipt_item_id' => 17,
                'qr_code' => 'QR-100017-B017',
                'batch_no' => 'B017-20231117',
                'location_id' => 2,
                'rack_id' => 17,
                'pallet_id' => 17,
                'quantity' => 450, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100018',
                'item_name' => 'Pivot',
                'goods_receipt_item_id' => 18,
                'qr_code' => 'QR-100018-B018',
                'batch_no' => 'B018-20231118',
                'location_id' => 2,
                'rack_id' => 18,
                'pallet_id' => 18,
                'quantity' => 150, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}