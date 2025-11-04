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
         * Catatan: Menggunakan item_code, goods_receipt_item_id, dan item_name.
         * Item Labels total 20 record. location_id hanya 1 dan 2.
         */
        DB::table('item_labels')->insert([
            // --- RAW MATERIAL (RM) - LOCATION 1 (Gudang Utama) ---
            [
                'item_code' => 'RM-001',
                'item_name' => 'Besi Batangan',
                'goods_receipt_item_id' => 1,
                'qr_code' => 'QR-RM001-B001',
                'batch_no' => 'B001-20231001',
                'location_id' => 1, 
                'rack_id' => 1,
                'pallet_id' => 1,
                'quantity' => 500, // KG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-002',
                'item_name' => 'Plastik Granul',
                'goods_receipt_item_id' => 2,
                'qr_code' => 'QR-RM002-B002',
                'batch_no' => 'B002-20230925',
                'location_id' => 1,
                'rack_id' => 2,
                'pallet_id' => 2,
                'quantity' => 1200, // KG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-007',
                'item_name' => 'Pigmen Warna Merah (Bubuk)',
                'goods_receipt_item_id' => 3,
                'qr_code' => 'QR-RM007-B004',
                'batch_no' => 'B004-20231015',
                'location_id' => 1, 
                'rack_id' => 4,
                'pallet_id' => 4,
                'quantity' => 25, // GR
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-010',
                'item_name' => 'Komponen Elektronik Resistor',
                'goods_receipt_item_id' => 4,
                'qr_code' => 'QR-RM010-B005',
                'batch_no' => 'B005-20231020',
                'location_id' => 1,
                'rack_id' => 5,
                'pallet_id' => 5,
                'quantity' => 5000, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-001',
                'item_name' => 'Besi Batangan',
                'goods_receipt_item_id' => 5,
                'qr_code' => 'QR-RM001-B011',
                'batch_no' => 'B011-20231101',
                'location_id' => 1,
                'rack_id' => 1, 
                'pallet_id' => 11,
                'quantity' => 350, // KG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-011',
                'item_name' => 'Minyak Pelumas Industri',
                'goods_receipt_item_id' => 6,
                'qr_code' => 'QR-RM011-B019',
                'batch_no' => 'B019-20230710',
                'location_id' => 1, 
                'rack_id' => 4,
                'pallet_id' => 19,
                'quantity' => 50, // LITER
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-004',
                'item_name' => 'Kawat Tembaga Tipis',
                'goods_receipt_item_id' => 7,
                'qr_code' => 'QR-RM004-B020',
                'batch_no' => 'B020-20231029',
                'location_id' => 1,
                'rack_id' => 16,
                'pallet_id' => 20,
                'quantity' => 200, // M
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- RAW MATERIAL (RM) - LOCATION 2 (Gudang Khusus) ---
            [
                'item_code' => 'RM-005',
                'item_name' => 'Kain Katun Putih',
                'goods_receipt_item_id' => 8,
                'qr_code' => 'QR-RM005-B003',
                'batch_no' => 'B003-20231101',
                'location_id' => 2, 
                'rack_id' => 3,
                'pallet_id' => 3,
                'quantity' => 850, // METER
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-006',
                'item_name' => 'Papan Kayu Jati',
                'goods_receipt_item_id' => 9,
                'qr_code' => 'QR-RM006-B014',
                'batch_no' => 'B014-20230815',
                'location_id' => 2,
                'rack_id' => 12,
                'pallet_id' => 14,
                'quantity' => 150, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'RM-009',
                'item_name' => 'Karet Lembaran Tebal',
                'goods_receipt_item_id' => 10,
                'qr_code' => 'QR-RM009-B015',
                'batch_no' => 'B015-20231025',
                'location_id' => 2,
                'rack_id' => 13,
                'pallet_id' => 15,
                'quantity' => 200, // M2
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],


            // --- FINISHED GOOD (FG) - LOCATION 1 (Gudang Utama) ---
            [
                'item_code' => 'FG-001',
                'item_name' => 'Botol Air Mineral 600ml',
                'goods_receipt_item_id' => 11,
                'qr_code' => 'QR-FG001-B006',
                'batch_no' => 'B006-PROD20231101',
                'location_id' => 1,
                'rack_id' => 6,
                'pallet_id' => 6,
                'quantity' => 1500, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-003',
                'item_name' => 'Kotak Karton Kemasan Medium',
                'goods_receipt_item_id' => 12,
                'qr_code' => 'QR-FG003-B007',
                'batch_no' => 'B007-PROD20231028',
                'location_id' => 1,
                'rack_id' => 7,
                'pallet_id' => 7,
                'quantity' => 800, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-004',
                'item_name' => 'Kabel Listrik 2x1.5mm Roll 50m',
                'goods_receipt_item_id' => 13,
                'qr_code' => 'QR-FG004-B008',
                'batch_no' => 'B008-PROD20231010',
                'location_id' => 1,
                'rack_id' => 8,
                'pallet_id' => 8,
                'quantity' => 50, // ROLL
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-004',
                'item_name' => 'Kabel Listrik 2x1.5mm Roll 50m',
                'goods_receipt_item_id' => 14,
                'qr_code' => 'QR-FG004-B013',
                'batch_no' => 'B013-PROD20230901',
                'location_id' => 1,
                'rack_id' => 8,
                'pallet_id' => 13,
                'quantity' => 75, // ROLL
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-008',
                'item_name' => 'Set Kunci Pas Mekanik (10pcs)',
                'goods_receipt_item_id' => 15,
                'qr_code' => 'QR-FG008-B016',
                'batch_no' => 'B016-PROD20231005',
                'location_id' => 1,
                'rack_id' => 14,
                'pallet_id' => 16,
                'quantity' => 100, // SET
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-001',
                'item_name' => 'Botol Air Mineral 600ml',
                'goods_receipt_item_id' => 16,
                'qr_code' => 'QR-FG001-B018',
                'batch_no' => 'B018-PROD20231104',
                'location_id' => 1,
                'rack_id' => 6,
                'pallet_id' => 18,
                'quantity' => 900, // PCS
                'status' => 'Holding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // --- FINISHED GOOD (FG) - LOCATION 2 (Gudang Khusus) ---
            [
                'item_code' => 'FG-005',
                'item_name' => 'Kaos Polos Pria Ukuran L',
                'goods_receipt_item_id' => 17,
                'qr_code' => 'QR-FG005-B009',
                'batch_no' => 'B009-PROD20231102',
                'location_id' => 2,
                'rack_id' => 9,
                'pallet_id' => 9,
                'quantity' => 300, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-005',
                'item_name' => 'Kaos Polos Pria Ukuran L',
                'goods_receipt_item_id' => 18,
                'qr_code' => 'QR-FG005-B012',
                'batch_no' => 'B012-PROD20231103',
                'location_id' => 2,
                'rack_id' => 11,
                'pallet_id' => 12,
                'quantity' => 150, // PCS
                'status' => 'Quality Check',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-006',
                'item_name' => 'Meja Kerja Kayu Minimalis',
                'goods_receipt_item_id' => 19,
                'qr_code' => 'QR-FG006-B010',
                'batch_no' => 'B010-PROD20231025',
                'location_id' => 2,
                'rack_id' => 10,
                'pallet_id' => 10,
                'quantity' => 30, // PCS
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => 'FG-007',
                'item_name' => 'Cat Tembok Interior 5L Merah',
                'goods_receipt_item_id' => 20,
                'qr_code' => 'QR-FG007-B017',
                'batch_no' => 'B017-PROD20230915',
                'location_id' => 2,
                'rack_id' => 15,
                'pallet_id' => 17,
                'quantity' => 80, // KALENG
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}