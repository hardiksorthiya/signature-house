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
        Schema::table('feeders', function (Blueprint $table) {
            // Add composite unique constraint on feeder + feeder_brand_id
            // This allows same feeder name with different brands
            $table->unique(['feeder', 'feeder_brand_id'], 'feeder_brand_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeders', function (Blueprint $table) {
            $table->dropUnique('feeder_brand_unique');
        });
    }
};
