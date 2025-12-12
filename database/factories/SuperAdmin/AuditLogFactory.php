<?php

namespace Database\Factories\SuperAdmin;

use App\Models\SuperAdmin\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['create', 'update', 'delete', 'view', 'export', 'import'];
        $modules = ['items', 'customers', 'suppliers', 'locations', 'inventory', 'goods_receipts', 'material_requests', 'users', 'roles'];

        return [
            'user_id' => \App\Models\User::factory(),
            'action' => fake()->randomElement($actions),
            'module' => fake()->randomElement($modules),
        ];
    }
}
