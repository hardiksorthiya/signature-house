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
        Schema::table('contracts', function (Blueprint $table) {
            // Other Buyer Expenses Details
            $table->string('overseas_freight')->nullable()->after('pan');
            $table->string('demurrage_detention_cfs_charges')->nullable();
            $table->string('air_pipe_connection')->nullable();
            $table->string('custom_duty')->nullable();
            $table->string('port_expenses_transport')->nullable();
            $table->string('crane_foundation')->nullable();
            $table->string('humidification')->nullable();
            $table->string('damage')->nullable();
            $table->string('gst_custom_charges')->nullable();
            $table->string('compressor')->nullable();
            $table->string('optional_spares')->nullable();
            $table->boolean('other_buyer_expenses_in_print')->default(true)->after('optional_spares');
            
            // Other Details
            $table->string('payment_terms')->nullable()->after('other_buyer_expenses_in_print');
            $table->string('quote_validity')->nullable();
            $table->string('loading_terms')->nullable();
            $table->string('warranty')->nullable();
            $table->string('complimentary_spares')->nullable();
            $table->boolean('other_details_in_print')->default(true)->after('complimentary_spares');
            
            // Difference of Specification
            $table->string('cam_jacquard_chain_jacquard')->nullable()->after('other_details_in_print');
            $table->string('hooks_5376_to_6144_jacquard')->nullable();
            $table->string('warp_beam')->nullable();
            $table->string('reed_space_380_to_420_cm')->nullable();
            $table->string('color_selector_8_to_12')->nullable();
            $table->string('hooks_5376_to_2688_jacquard')->nullable();
            $table->string('extra_feeder')->nullable();
            $table->boolean('difference_specification_in_print')->default(true)->after('extra_feeder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'overseas_freight',
                'demurrage_detention_cfs_charges',
                'air_pipe_connection',
                'custom_duty',
                'port_expenses_transport',
                'crane_foundation',
                'humidification',
                'damage',
                'gst_custom_charges',
                'compressor',
                'optional_spares',
                'other_buyer_expenses_in_print',
                'payment_terms',
                'quote_validity',
                'loading_terms',
                'warranty',
                'complimentary_spares',
                'other_details_in_print',
                'cam_jacquard_chain_jacquard',
                'hooks_5376_to_6144_jacquard',
                'warp_beam',
                'reed_space_380_to_420_cm',
                'color_selector_8_to_12',
                'hooks_5376_to_2688_jacquard',
                'extra_feeder',
                'difference_specification_in_print',
            ]);
        });
    }
};
