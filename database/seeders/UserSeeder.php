<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users with different roles
        \App\Models\User::factory()->count(10)->create([
            'role' => 'Super Admin',
        ]);

        \App\Models\User::factory()->count(5)->create([
            'role' => 'Admin Inbound',
        ]);

        \App\Models\User::factory()->count(5)->create([
            'role' => 'Admin Inventory',
        ]);

        \App\Models\User::factory()->count(5)->create([
            'role' => 'Admin Production',
        ]);
    }
}
