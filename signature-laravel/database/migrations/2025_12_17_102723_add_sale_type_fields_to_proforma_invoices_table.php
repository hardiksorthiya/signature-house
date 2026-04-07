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
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->enum('type_of_sale', ['import', 'local', 'high_seas'])->nullable()->after('notes');
            $table->string('currency', 10)->default('USD')->after('type_of_sale');
            $table->decimal('usd_rate', 10, 2)->nullable()->after('currency');
            $table->decimal('commission', 5, 2)->nullable()->after('usd_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn(['type_of_sale', 'currency', 'usd_rate', 'commission']);
        });
    }
};
