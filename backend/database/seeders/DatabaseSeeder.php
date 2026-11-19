<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * There are no default accounts: the first admin comes from ADMIN_EMAIL / ADMIN_PASSWORD
     * in the local .env (or from `php artisan admin:create`) and gets the Super Admin role.
     */
    public function run(): void
    {
        $this->call([ReferenceDataSeeder::class, StaffSeeder::class]);

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD are not set; no admin user seeded.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => $password,
                'status' => true,
                'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
            ],
        );
    }
}
