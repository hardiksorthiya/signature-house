<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            
            // Reports
            'view reports',
            'export reports',
            
            // Settings
            'view settings',
            'edit settings',
            
            // Contract Approval
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            
            // Customer Management
            'view customers',
            'delete customers',
            
            // Lead Management
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'convert contract',
            
            // Proforma Invoice Management
            'view proforma invoices',
            'create proforma invoices',
            'edit proforma invoices',
            'delete proforma invoices',
            
            // Over Invoice Management
            'view over invoice',
            'create over invoice',
            'edit over invoice',
            'delete over invoice',
            
            // Delivery Detail Management
            'view delivery detail',
            'create delivery detail',
            'edit delivery detail',
            'delete delivery detail',
            
            // Status Management
            'view status',
            'create status',
            'edit status',
            'delete status',
            
            // Pre Erection Management
            'view pre erection',
            'create pre erection',
            'edit pre erection',
            'delete pre erection',
            
            // Image Uploading Management
            'view image uploading',
            'create image uploading',
            'edit image uploading',
            'delete image uploading',
            
            // Damage Management
            'view damage',
            'create damage',
            'edit damage',
            'delete damage',
            
            // Serial Number Management
            'view serial number',
            'create serial number',
            'edit serial number',
            'delete serial number',
            
            // Machine Erection Management
            'view machine erection',
            'create machine erection',
            'edit machine erection',
            'delete machine erection',
            
            // IA Fitting Management
            'view ia fitting',
            'create ia fitting',
            'edit ia fitting',
            'delete ia fitting',
            
            // MS Unloading Spare List Management
            'view spare list',
            'create spare list',
            'edit spare list',
            'delete spare list',
            
            // Spare (master data) Management
            'view spare',
            'create spare',
            'edit spare',
            'delete spare',
            
            // Payment Management
            'view payment',
            'create payment',
            'edit payment',
            'delete payment',
            
            // Purchase Order Management
            'view purchase order',
            'create purchase order',
            'edit purchase order',
            'delete purchase order',
            
            // Inventory Management
            'view inventory',
            'create inventory',
            'edit inventory',
            'delete inventory',
            
            // Task Management
            'view task',
            'create task',
            'edit task',
            'delete task',

            // Old Data Management
            'view old data',
            'create old data',
            'edit old data',
            'delete old data',

            // Complain Management
            'view complain',
            'create complain',
            'edit complain',
            'delete complain',

            // Employee location (track on map)
            'view employee location',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except role management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view reports',
            'export reports',
            'view settings',
            'edit settings',
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            'view customers',
            'view spare',
            'create spare',
            'edit spare',
            'delete spare',
            'view employee location',
            'view damage',
            'create damage',
            'edit damage',
            'delete damage',
        ]);

        // Manager - View and edit permissions
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view users',
            'view reports',
            'export reports',
            'view contract approvals',
            'approve contracts',
            'reject contracts',
            'view customers',
        ]);

        // Staff - Limited permissions
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        // No specific permissions for Staff

        // User - Basic permissions
        $user = Role::firstOrCreate(['name' => 'User']);
        // No specific permissions for User
        
        // Department Lead Manager - Role for department users with lead management permissions
        $departmentLeadManager = Role::firstOrCreate(['name' => 'Department Lead Manager']);
        $departmentLeadManager->givePermissionTo([
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'convert contract',
            'view contract approvals', // Allow viewing contracts they created
            'view customers', // Allow viewing customers from approved contracts
        ]);

        // Engineers - for complaint assignment
        Role::firstOrCreate(['name' => 'Junior Engineer']);
        Role::firstOrCreate(['name' => 'Senior Engineer']);
    }
}
