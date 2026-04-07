<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spares', function (Blueprint $table) {
            // Only add the column if it doesn't already exist
            if (!Schema::hasColumn('spares', 'quantity')) {
                $table->integer('quantity')->default(0)->after('spare_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spares', function (Blueprint $table) {
            // Only drop the column if it exists
            if (Schema::hasColumn('spares', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
