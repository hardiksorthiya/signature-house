<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('assigned_at');
        });

        DB::table('complaints')
            ->where('status', 'completed')
            ->whereNull('completed_at')
            ->update(['completed_at' => DB::raw('updated_at')]);

        DB::table('complaints')
            ->whereIn('id', function ($query) {
                $query->select('complaint_id')->from('complaint_assignees');
            })
            ->whereNull('assigned_at')
            ->update(['assigned_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['assigned_at', 'completed_at']);
        });
    }
};
