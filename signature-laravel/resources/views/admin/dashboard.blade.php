<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Admin Panel</h3>
                    <p class="mb-4">This is the admin dashboard. Only users with Admin or Super Admin role can access this page.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="p-4 bg-blue-500 text-white rounded-lg">
                            <h4 class="font-semibold">User Management</h4>
                            <p class="text-sm mt-2">Manage users and their roles</p>
                        </div>
                        <div class="p-4 bg-green-500 text-white rounded-lg">
                            <h4 class="font-semibold">System Settings</h4>
                            <p class="text-sm mt-2">Configure system settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



