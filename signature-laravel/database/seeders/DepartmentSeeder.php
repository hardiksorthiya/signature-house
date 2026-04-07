<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create example department
        $department = Department::firstOrCreate(
            ['name' => 'Sales Department'],
            ['description' => 'Department responsible for managing leads and converting them to contracts']
        );

        $this->command->info("Created department: {$department->name} (ID: {$department->id})");

        // Get the Department Lead Manager role
        $role = Role::where('name', 'Department Lead Manager')->first();

        if ($role) {
            // Example: Assign role to all users in this department
            $users = User::where('department_id', $department->id)->get();
            
            if ($users->isEmpty()) {
                $this->command->warn("No users found in department. To assign users to this department, update their department_id field.");
            } else {
                foreach ($users as $user) {
                    $user->assignRole($role);
                    $this->command->info("Assigned role '{$role->name}' to user: {$user->name} ({$user->email})");
                }
            }
        } else {
            $this->command->warn("Role 'Department Lead Manager' not found. Please run RolePermissionSeeder first.");
        }
    }
}
