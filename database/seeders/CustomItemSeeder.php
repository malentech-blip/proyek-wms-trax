<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CustomItem::factory()->count(100)->create();
    }
}
