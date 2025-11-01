<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create audit logs for users
        $users = \App\Models\User::all();

        foreach ($users->take(100) as $user) {
            \App\Models\SuperAdmin\AuditLog::factory()
                ->count(1)
                ->create([
                    'user_id' => $user->id,
                ]);
        }
    }
}
