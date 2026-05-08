<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('global_not_included_in_offer_in_print')->default(true)->after('global_terms_conditions_in_print');
            $table->json('global_not_included_in_offer')->nullable()->after('global_not_included_in_offer_in_print');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->boolean('not_included_in_offer_in_print')->default(true)->after('terms_conditions_in_print');
            $table->json('not_included_in_offer')->nullable()->after('not_included_in_offer_in_print');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['global_not_included_in_offer_in_print', 'global_not_included_in_offer']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['not_included_in_offer_in_print', 'not_included_in_offer']);
        });
    }
};
