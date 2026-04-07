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
        Schema::table('contract_machines', function (Blueprint $table) {
            $table->foreignId('delivery_term_id')->nullable()->after('machine_heald_wire_id')->constrained('delivery_terms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_machines', function (Blueprint $table) {
            $table->dropForeign(['delivery_term_id']);
            $table->dropColumn('delivery_term_id');
        });
    }
};
