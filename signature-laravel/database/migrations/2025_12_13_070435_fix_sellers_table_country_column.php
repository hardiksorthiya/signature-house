<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if 'country' column exists and drop it if it does
        if (Schema::hasColumn('sellers', 'country')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }

        // Ensure country_id exists and is properly configured
        if (!Schema::hasColumn('sellers', 'country_id')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained()->onDelete('set null');
            });
        } else {
            // Make sure country_id is nullable if it exists
            Schema::table('sellers', function (Blueprint $table) {
                $table->foreignId('country_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is safe to reverse
        // The previous migration should handle the rollback
    }
};
