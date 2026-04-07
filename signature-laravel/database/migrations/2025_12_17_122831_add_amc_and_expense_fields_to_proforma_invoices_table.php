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
            $table->decimal('overseas_freight', 15, 2)->nullable()->after('shipping_address');
            $table->decimal('port_expenses_clearing', 15, 2)->nullable()->after('overseas_freight');
            $table->decimal('gst_percentage', 5, 2)->default(18)->after('port_expenses_clearing');
            $table->decimal('gst_amount', 15, 2)->nullable()->after('gst_percentage');
            $table->decimal('final_amount_with_gst', 15, 2)->nullable()->after('gst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'overseas_freight',
                'port_expenses_clearing',
                'gst_percentage',
                'gst_amount',
                'final_amount_with_gst'
            ]);
        });
    }
};
