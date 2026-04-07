<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaint_spare', function (Blueprint $table) {
            $table->timestamp('used_at')->nullable()->after('quantity');
        });
        DB::table('complaint_spare')->whereNull('used_at')->update(['used_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_spare', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }
};
