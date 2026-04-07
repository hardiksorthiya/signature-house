<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create default admin user
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Super Admin');

        // Create test users with different roles
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'phone' => '1234567891',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole('Manager');

        $staff = User::factory()->create([
            'name' => 'Staff User',
            'phone' => '1234567892',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('Staff');
    }
}
