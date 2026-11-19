<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Roles, positions and departments as they exist in sistem.kuryer.net (spec §9.1, §9.2).
 * The spec gives only each role's permission count, not the keys, so roles other than
 * Super Admin start empty and are filled in on the Rollar page. Idempotent by name.
 */
class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Operator' => 'operator',
            'Branch' => 'branch',
            'Delivery Man' => 'delivery-man',
            'Əməliyyatçı' => 'emeliyyatci',
            'Super Admin' => Role::SUPER_ADMIN,
        ];

        foreach ($roles as $name => $slug) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name, 'permissions' => []]);
        }

        foreach (['Chief Marketing Officer (CMO)', 'Branch User', 'Warehouseman', 'Deliveryman', 'Cashier', 'Courier', 'Operator'] as $title) {
            Designation::firstOrCreate(['title' => $title]);
        }

        Department::firstOrCreate(['title' => 'General Management']);
    }
}
