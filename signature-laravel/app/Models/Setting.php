<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        // Global Contract Details - Other Buyer Expenses
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
        // Global Contract Details - Other Details
        'global_payment_terms',
        'global_quote_validity',
        'global_loading_terms',
        'global_warranty',
        'global_complimentary_spares',
        'global_other_details_in_print',
        // Global Contract Details - Difference of Specification
        'global_spec_color_8_to_12_selectors',
        'global_spec_extra_feeder_per_pc',
        'global_spec_extra_warp_beam_per_pc',
        'global_spec_reed_reduction_per_20cm',
        'global_spec_reed_increase_per_20cm',
        'global_spec_increase_380_to_480cm',
        'global_spec_electronic_weft_cutter',
        'global_spec_hooks_5376_to_6144',
        'global_spec_hooks_5376_to_10240',
        'global_spec_hooks_5376_to_2688',
        'global_spec_changshu_to_sns_cam',
        'global_spec_changshu_to_sns_chain_24',
        'global_spec_changshu_to_sns_chain_16',
        'global_spec_changshu_to_jkd_or_changfang',
        'global_spec_changshu_to_wumu',
        'global_spec_bintian_8_shaft_cam_heald',
        'global_spec_bintian_10_shaft_cam_heald',
        'global_spec_changshu_16_shaft_2861_dobby',
        'global_spec_staubli_16_shaft_dobby',
        'global_spec_increase_1_nozzle',
        'global_spec_increase_1_feeder',
        'global_spec_reed_increase_per_10cm',
        'global_spec_bintian_cam_plate_extra',
        'global_spec_bintian_gear_extra',
        'global_difference_specification_in_print',
        'global_difference_specification_extended_in_print',
        'global_spec_3_niupai_10_shaft_410_cam_heald_frames',
        'global_spec_3_niupai_12_shaft_411_cam_heald_frames',
        'global_spec_3_niupai_16_electronic_dobby_5400d_invertor',
        'global_spec_3_sanhe_s650_controller_accessories',
        'global_spec_3_increase_1_colour',
        'global_spec_3_increase_1_feeder',
        'global_spec_3_reed_increase_per_20cm',
        'global_spec_3_single_pump_to_double_pump',
        'global_difference_specification_3_in_print',
        // Global Contract Details - Terms & conditions
        'global_terms_government_policies',
        'global_terms_currency',
        'global_terms_licenses_bank_payment',
        'global_terms_demurrage_detentions',
        'global_terms_cancellation_order',
        'global_terms_jurisdiction_seller_rights',
        'global_terms_conditions_in_print',
    ];

    protected $casts = [
        'global_other_buyer_expenses_in_print' => 'boolean',
        'global_other_details_in_print' => 'boolean',
        'global_difference_specification_in_print' => 'boolean',
        'global_difference_specification_extended_in_print' => 'boolean',
        'global_difference_specification_3_in_print' => 'boolean',
        'global_terms_conditions_in_print' => 'boolean',
    ];
}
