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
        Schema::table('proforma_invoice_machines', function (Blueprint $table) {
            $table->decimal('amc_price', 15, 2)->default(0)->after('amount');
            $table->decimal('pi_price_plus_amc', 15, 2)->nullable()->after('amc_price');
            $table->decimal('total_pi_price', 15, 2)->nullable()->after('pi_price_plus_amc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_invoice_machines', function (Blueprint $table) {
            $table->dropColumn([
                'amc_price',
                'pi_price_plus_amc',
                'total_pi_price'
            ]);
        });
    }
};
