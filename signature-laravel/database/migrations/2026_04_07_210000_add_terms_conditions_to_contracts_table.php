<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('terms_government_policies')->nullable()->after('difference_specification_3_in_print');
            $table->text('terms_currency')->nullable();
            $table->text('terms_licenses_bank_payment')->nullable();
            $table->text('terms_demurrage_detentions')->nullable();
            $table->text('terms_cancellation_order')->nullable();
            $table->text('terms_jurisdiction_seller_rights')->nullable();
            $table->boolean('terms_conditions_in_print')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'terms_government_policies',
                'terms_currency',
                'terms_licenses_bank_payment',
                'terms_demurrage_detentions',
                'terms_cancellation_order',
                'terms_jurisdiction_seller_rights',
                'terms_conditions_in_print',
            ]);
        });
    }
};
