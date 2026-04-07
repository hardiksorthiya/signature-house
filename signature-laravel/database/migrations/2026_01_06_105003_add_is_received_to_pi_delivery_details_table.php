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
        Schema::table('pi_delivery_details', function (Blueprint $table) {
            $table->boolean('is_received')->default(false)->after('no_of_copies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pi_delivery_details', function (Blueprint $table) {
            $table->dropColumn('is_received');
        });
    }
};
