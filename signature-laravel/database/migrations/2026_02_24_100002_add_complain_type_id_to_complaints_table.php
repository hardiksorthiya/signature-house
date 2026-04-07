<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('complain_type_id')->nullable()->after('contract_id')->constrained('complain_types')->cascadeOnDelete();
        });

        // Migrate existing electric/mechanical to complain_types
        $electricId = DB::table('complain_types')->where('name', 'Electric')->value('id');
        $mechanicalId = DB::table('complain_types')->where('name', 'Mechanical')->value('id');
        if ($electricId) {
            DB::table('complaints')->where('complain_type', 'electric')->update(['complain_type_id' => $electricId]);
        }
        if ($mechanicalId) {
            DB::table('complaints')->where('complain_type', 'mechanical')->update(['complain_type_id' => $mechanicalId]);
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('complain_type');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('complain_type')->nullable()->after('contract_id');
        });
        $electricId = DB::table('complain_types')->where('name', 'Electric')->value('id');
        $mechanicalId = DB::table('complain_types')->where('name', 'Mechanical')->value('id');
        if ($electricId) {
            DB::table('complaints')->where('complain_type_id', $electricId)->update(['complain_type' => 'electric']);
        }
        if ($mechanicalId) {
            DB::table('complaints')->where('complain_type_id', $mechanicalId)->update(['complain_type' => 'mechanical']);
        }
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['complain_type_id']);
        });
    }
};
