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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('global_terms_government_policies')->nullable()->after('global_difference_specification_in_print');
            $table->text('global_terms_currency')->nullable();
            $table->text('global_terms_licenses_bank_payment')->nullable();
            $table->text('global_terms_demurrage_detentions')->nullable();
            $table->text('global_terms_cancellation_order')->nullable();
            $table->text('global_terms_jurisdiction_seller_rights')->nullable();
            $table->boolean('global_terms_conditions_in_print')->default(true)->after('global_terms_jurisdiction_seller_rights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'global_terms_government_policies',
                'global_terms_currency',
                'global_terms_licenses_bank_payment',
                'global_terms_demurrage_detentions',
                'global_terms_cancellation_order',
                'global_terms_jurisdiction_seller_rights',
                'global_terms_conditions_in_print',
            ]);
        });
    }
};
