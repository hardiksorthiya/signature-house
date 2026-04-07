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
            // Other Buyer Expenses Details - Global Defaults
            $table->string('global_overseas_freight')->nullable()->after('secondary_color');
            $table->string('global_demurrage_detention_cfs_charges')->nullable();
            $table->string('global_air_pipe_connection')->nullable();
            $table->string('global_custom_duty')->nullable();
            $table->string('global_port_expenses_transport')->nullable();
            $table->string('global_crane_foundation')->nullable();
            $table->string('global_humidification')->nullable();
            $table->string('global_damage')->nullable();
            $table->string('global_gst_custom_charges')->nullable();
            $table->string('global_compressor')->nullable();
            $table->string('global_optional_spares')->nullable();
            $table->boolean('global_other_buyer_expenses_in_print')->default(true)->after('global_optional_spares');
            
            // Other Details - Global Defaults
            $table->string('global_payment_terms')->nullable()->after('global_other_buyer_expenses_in_print');
            $table->string('global_quote_validity')->nullable();
            $table->string('global_loading_terms')->nullable();
            $table->string('global_warranty')->nullable();
            $table->string('global_complimentary_spares')->nullable();
            $table->boolean('global_other_details_in_print')->default(true)->after('global_complimentary_spares');
            
            // Difference of Specification - Global Defaults
            $table->string('global_cam_jacquard_chain_jacquard')->nullable()->after('global_other_details_in_print');
            $table->string('global_hooks_5376_to_6144_jacquard')->nullable();
            $table->string('global_warp_beam')->nullable();
            $table->string('global_reed_space_380_to_420_cm')->nullable();
            $table->string('global_color_selector_8_to_12')->nullable();
            $table->string('global_hooks_5376_to_2688_jacquard')->nullable();
            $table->string('global_extra_feeder')->nullable();
            $table->boolean('global_difference_specification_in_print')->default(true)->after('global_extra_feeder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'global_overseas_freight',
                'global_demurrage_detention_cfs_charges',
                'global_air_pipe_connection',
                'global_custom_duty',
                'global_port_expenses_transport',
                'global_crane_foundation',
                'global_humidification',
                'global_damage',
                'global_gst_custom_charges',
                'global_compressor',
                'global_optional_spares',
                'global_other_buyer_expenses_in_print',
                'global_payment_terms',
                'global_quote_validity',
                'global_loading_terms',
                'global_warranty',
                'global_complimentary_spares',
                'global_other_details_in_print',
                'global_cam_jacquard_chain_jacquard',
                'global_hooks_5376_to_6144_jacquard',
                'global_warp_beam',
                'global_reed_space_380_to_420_cm',
                'global_color_selector_8_to_12',
                'global_hooks_5376_to_2688_jacquard',
                'global_extra_feeder',
                'global_difference_specification_in_print',
            ]);
        });
    }
};
