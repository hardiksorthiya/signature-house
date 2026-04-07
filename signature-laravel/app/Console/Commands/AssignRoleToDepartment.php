<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoleToDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department:assign-role {department : The name or ID of the department} {role : The name of the role to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to all users in a specific department';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $departmentIdentifier = $this->argument('department');
        $roleName = $this->argument('role');

        // Find department by ID or name
        $department = is_numeric($departmentIdentifier)
            ? Department::find($departmentIdentifier)
            : Department::where('name', $departmentIdentifier)->first();

        if (!$department) {
            $this->error("Department not found: {$departmentIdentifier}");
            return 1;
        }

        // Check if role exists
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role not found: {$roleName}");
            return 1;
        }

        // Get all users in the department
        $users = User::where('department_id', $department->id)->get();

        if ($users->isEmpty()) {
            $this->warn("No users found in department: {$department->name}");
            return 0;
        }

        // Assign role to all users
        $count = 0;
        foreach ($users as $user) {
            $user->assignRole($role);
            $count++;
        }

        $this->info("Successfully assigned role '{$roleName}' to {$count} user(s) in department '{$department->name}'");
        return 0;
    }
}
