<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MaterialRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin\Production\MaterialRequest::factory()->count(100)->create();
    }
}
