<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['complaint_id', 'user_id']);
        });

        foreach (DB::table('complaints')->whereNotNull('assigned_to_id')->get() as $row) {
            DB::table('complaint_assignees')->insertOrIgnore([
                'complaint_id' => $row->id,
                'user_id' => $row->assigned_to_id,
            ]);
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_id']);
            $table->dropColumn('assigned_to_id');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('assigned_to_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        $first = DB::table('complaint_assignees')->select('complaint_id', 'user_id')->orderBy('id')->get()->groupBy('complaint_id');
        foreach ($first as $complaintId => $rows) {
            $userId = $rows->first()->user_id;
            DB::table('complaints')->where('id', $complaintId)->update(['assigned_to_id' => $userId]);
        }

        Schema::dropIfExists('complaint_assignees');
    }
};
