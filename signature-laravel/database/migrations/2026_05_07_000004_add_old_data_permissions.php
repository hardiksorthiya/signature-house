<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'view old data',
            'create old data',
            'edit old data',
            'delete old data',
        ];

        $now = now();
        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', ['view old data', 'create old data', 'edit old data', 'delete old data'])
            ->delete();
    }
};
