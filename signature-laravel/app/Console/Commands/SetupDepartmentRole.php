<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SetupDepartmentRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department:setup {user_email? : Email of the user to assign} {department_name? : Name of the department}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a user to a department and assign the Department Lead Manager role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->argument('user_email');
        $departmentName = $this->argument('department_name') ?? 'Sales Department';

        // Find or create department
        $department = Department::firstOrCreate(
            ['name' => $departmentName],
            ['description' => 'Department responsible for managing leads and converting them to contracts']
        );

        $this->info("Using department: {$department->name} (ID: {$department->id})");

        // If no user email provided, list users and ask
        if (!$userEmail) {
            $users = User::all();
            if ($users->isEmpty()) {
                $this->error('No users found in the system.');
                return 1;
            }

            $this->info('Available users:');
            $userChoices = [];
            foreach ($users as $index => $user) {
                $choiceNum = $index + 1;
                $userChoices[$choiceNum] = $user->email;
                $this->line("  [{$choiceNum}] {$user->name} ({$user->email})");
            }

            $choice = $this->ask('Select user number to assign', 1);
            $userEmail = $userChoices[$choice] ?? null;

            if (!$userEmail) {
                $this->error('Invalid selection.');
                return 1;
            }
        }

        // Find user
        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $this->error("User not found: {$userEmail}");
            return 1;
        }

        // Check if role exists
        $role = Role::where('name', 'Department Lead Manager')->first();
        if (!$role) {
            $this->error("Role 'Department Lead Manager' not found. Please run: php artisan db:seed --class=RolePermissionSeeder");
            return 1;
        }

        // Assign department
        $user->update(['department_id' => $department->id]);
        $this->info("Assigned user to department: {$department->name}");

        // Assign role
        $user->assignRole($role);
        $this->info("Assigned role: {$role->name}");

        // Display permissions
        $permissions = $role->permissions->pluck('name')->toArray();
        $this->info("\nUser now has the following permissions:");
        foreach ($permissions as $permission) {
            $this->line("  - {$permission}");
        }

        $this->info("\n✓ Setup complete! User '{$user->name}' can now manage leads and convert contracts.");

        return 0;
    }
}
