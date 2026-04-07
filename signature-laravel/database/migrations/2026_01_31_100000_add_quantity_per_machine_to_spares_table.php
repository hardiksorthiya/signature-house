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
            if (!Schema::hasColumn('spares', 'quantity_per_machine')) {
                $table->unsignedInteger('quantity_per_machine')->default(1)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spares', function (Blueprint $table) {
            if (Schema::hasColumn('spares', 'quantity_per_machine')) {
                $table->dropColumn('quantity_per_machine');
            }
        });
    }
};
