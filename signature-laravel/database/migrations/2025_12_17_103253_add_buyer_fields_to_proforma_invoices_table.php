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
            $table->string('buyer_company_name')->nullable()->after('commission');
            $table->string('pan')->nullable()->after('buyer_company_name');
            $table->string('gst')->nullable()->after('pan');
            $table->string('phone_number')->nullable()->after('gst');
            $table->string('phone_number_2')->nullable()->after('phone_number');
            $table->string('ifc_certificate_number')->nullable()->after('phone_number_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn(['buyer_company_name', 'pan', 'gst', 'phone_number', 'phone_number_2', 'ifc_certificate_number']);
        });
    }
};
