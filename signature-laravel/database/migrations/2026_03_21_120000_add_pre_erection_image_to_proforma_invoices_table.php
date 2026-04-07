<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->string('pre_erection_image', 512)->nullable()->after('final_amount_with_gst');
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn('pre_erection_image');
        });
    }
};
