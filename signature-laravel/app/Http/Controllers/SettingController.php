<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings page.
     */
    public function edit()
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'primary_color' => '#9f2323',
                'secondary_color' => '#e80202',
            ]
        );

        return view('admin.settings', compact('setting'));
    }

    /**
     * Update branding and theme settings.
     */
    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'primary_color' => '#9f2323',
                'secondary_color' => '#e80202',
            ]
        );

        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:2048',
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
        ]);

        $data = [
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
        ];

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }

            $data['logo'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $data['favicon'] = $request->file('favicon')->store('branding', 'public');
        }

        $setting->update($data);

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the global contract details settings page.
     */
    public function contractDetails()
    {
        $setting = Setting::firstOrCreate(['id' => 1]);
        return view('admin.contract-details-settings', compact('setting'));
    }

    /**
     * Update global contract details settings.
     */
    public function updateContractDetails(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $request->validate([
            // Other Buyer Expenses Details
            'global_overseas_freight' => 'nullable|string|max:255',
            'global_demurrage_detention_cfs_charges' => 'nullable|string|max:255',
            'global_air_pipe_connection' => 'nullable|string|max:255',
            'global_custom_duty' => 'nullable|string|max:255',
            'global_port_expenses_transport' => 'nullable|string|max:255',
            'global_crane_foundation' => 'nullable|string|max:255',
            'global_humidification' => 'nullable|string|max:255',
            'global_damage' => 'nullable|string|max:255',
            'global_gst_custom_charges' => 'nullable|string|max:255',
            'global_compressor' => 'nullable|string|max:255',
            'global_optional_spares' => 'nullable|string|max:255',
            'global_other_buyer_expenses_in_print' => 'nullable|boolean',
            // Other Details
            'global_payment_terms' => 'nullable|string|max:255',
            'global_quote_validity' => 'nullable|string|max:255',
            'global_loading_terms' => 'nullable|string|max:255',
            'global_warranty' => 'nullable|string|max:255',
            'global_complimentary_spares' => 'nullable|string|max:255',
            'global_other_details_in_print' => 'nullable|boolean',
            // Difference of Specification
            'global_spec_color_8_to_12_selectors' => 'nullable|string|max:255',
            'global_spec_extra_feeder_per_pc' => 'nullable|string|max:255',
            'global_spec_extra_warp_beam_per_pc' => 'nullable|string|max:255',
            'global_spec_reed_reduction_per_20cm' => 'nullable|string|max:255',
            'global_spec_reed_increase_per_20cm' => 'nullable|string|max:255',
            'global_spec_increase_380_to_480cm' => 'nullable|string|max:255',
            'global_spec_electronic_weft_cutter' => 'nullable|string|max:255',
            'global_spec_hooks_5376_to_6144' => 'nullable|string|max:255',
            'global_spec_hooks_5376_to_10240' => 'nullable|string|max:255',
            'global_spec_hooks_5376_to_2688' => 'nullable|string|max:255',
            'global_spec_changshu_to_sns_cam' => 'nullable|string|max:255',
            'global_spec_changshu_to_sns_chain_24' => 'nullable|string|max:255',
            'global_spec_changshu_to_sns_chain_16' => 'nullable|string|max:255',
            'global_spec_changshu_to_jkd_or_changfang' => 'nullable|string|max:255',
            'global_spec_changshu_to_wumu' => 'nullable|string|max:255',
            'global_spec_bintian_8_shaft_cam_heald' => 'nullable|string|max:255',
            'global_spec_bintian_10_shaft_cam_heald' => 'nullable|string|max:255',
            'global_spec_changshu_16_shaft_2861_dobby' => 'nullable|string|max:255',
            'global_spec_staubli_16_shaft_dobby' => 'nullable|string|max:255',
            'global_spec_increase_1_nozzle' => 'nullable|string|max:255',
            'global_spec_increase_1_feeder' => 'nullable|string|max:255',
            'global_spec_reed_increase_per_10cm' => 'nullable|string|max:255',
            'global_spec_bintian_cam_plate_extra' => 'nullable|string|max:255',
            'global_spec_bintian_gear_extra' => 'nullable|string|max:255',
            'global_difference_specification_in_print' => 'nullable|boolean',
            'global_difference_specification_extended_in_print' => 'nullable|boolean',
            'global_spec_3_niupai_10_shaft_410_cam_heald_frames' => 'nullable|string|max:255',
            'global_spec_3_niupai_12_shaft_411_cam_heald_frames' => 'nullable|string|max:255',
            'global_spec_3_niupai_16_electronic_dobby_5400d_invertor' => 'nullable|string|max:255',
            'global_spec_3_sanhe_s650_controller_accessories' => 'nullable|string|max:255',
            'global_spec_3_increase_1_colour' => 'nullable|string|max:255',
            'global_spec_3_increase_1_feeder' => 'nullable|string|max:255',
            'global_spec_3_reed_increase_per_20cm' => 'nullable|string|max:255',
            'global_spec_3_single_pump_to_double_pump' => 'nullable|string|max:255',
            'global_difference_specification_3_in_print' => 'nullable|boolean',
            // Terms & conditions
            'global_terms_government_policies' => 'nullable|string|max:65535',
            'global_terms_currency' => 'nullable|string|max:65535',
            'global_terms_licenses_bank_payment' => 'nullable|string|max:65535',
            'global_terms_demurrage_detentions' => 'nullable|string|max:65535',
            'global_terms_cancellation_order' => 'nullable|string|max:65535',
            'global_terms_jurisdiction_seller_rights' => 'nullable|string|max:65535',
            'global_terms_conditions_in_print' => 'nullable|boolean',
            'global_not_included_in_offer_in_print' => 'nullable|boolean',
        ]);

        $showObe = config('contract.show_other_buyer_expenses_section', false);

        $setting->update([
            // Other Buyer Expenses Details (unchanged in DB when section hidden)
            'global_overseas_freight' => $showObe ? $request->global_overseas_freight : $setting->global_overseas_freight,
            'global_demurrage_detention_cfs_charges' => $showObe ? $request->global_demurrage_detention_cfs_charges : $setting->global_demurrage_detention_cfs_charges,
            'global_air_pipe_connection' => $showObe ? $request->global_air_pipe_connection : $setting->global_air_pipe_connection,
            'global_custom_duty' => $showObe ? $request->global_custom_duty : $setting->global_custom_duty,
            'global_port_expenses_transport' => $showObe ? $request->global_port_expenses_transport : $setting->global_port_expenses_transport,
            'global_crane_foundation' => $showObe ? $request->global_crane_foundation : $setting->global_crane_foundation,
            'global_humidification' => $showObe ? $request->global_humidification : $setting->global_humidification,
            'global_damage' => $showObe ? $request->global_damage : $setting->global_damage,
            'global_gst_custom_charges' => $showObe ? $request->global_gst_custom_charges : $setting->global_gst_custom_charges,
            'global_compressor' => $showObe ? $request->global_compressor : $setting->global_compressor,
            'global_optional_spares' => $showObe ? $request->global_optional_spares : $setting->global_optional_spares,
            'global_other_buyer_expenses_in_print' => $showObe
                ? ($request->has('global_other_buyer_expenses_in_print') ? (bool) $request->global_other_buyer_expenses_in_print : true)
                : (bool) ($setting->global_other_buyer_expenses_in_print ?? true),
            // Other Details
            'global_payment_terms' => $request->global_payment_terms,
            'global_quote_validity' => $request->global_quote_validity,
            'global_loading_terms' => $request->global_loading_terms,
            'global_warranty' => $request->global_warranty,
            'global_complimentary_spares' => $request->global_complimentary_spares,
            'global_other_details_in_print' => $request->has('global_other_details_in_print') ? (bool)$request->global_other_details_in_print : true,
            // Difference of Specification
            'global_spec_color_8_to_12_selectors' => $request->global_spec_color_8_to_12_selectors,
            'global_spec_extra_feeder_per_pc' => $request->global_spec_extra_feeder_per_pc,
            'global_spec_extra_warp_beam_per_pc' => $request->global_spec_extra_warp_beam_per_pc,
            'global_spec_reed_reduction_per_20cm' => $request->global_spec_reed_reduction_per_20cm,
            'global_spec_reed_increase_per_20cm' => $request->global_spec_reed_increase_per_20cm,
            'global_spec_increase_380_to_480cm' => $request->global_spec_increase_380_to_480cm,
            'global_spec_electronic_weft_cutter' => $request->global_spec_electronic_weft_cutter,
            'global_spec_hooks_5376_to_6144' => $request->global_spec_hooks_5376_to_6144,
            'global_spec_hooks_5376_to_10240' => $request->global_spec_hooks_5376_to_10240,
            'global_spec_hooks_5376_to_2688' => $request->global_spec_hooks_5376_to_2688,
            'global_spec_changshu_to_sns_cam' => $request->global_spec_changshu_to_sns_cam,
            'global_spec_changshu_to_sns_chain_24' => $request->global_spec_changshu_to_sns_chain_24,
            'global_spec_changshu_to_sns_chain_16' => $request->global_spec_changshu_to_sns_chain_16,
            'global_spec_changshu_to_jkd_or_changfang' => $request->global_spec_changshu_to_jkd_or_changfang,
            'global_spec_changshu_to_wumu' => $request->global_spec_changshu_to_wumu,
            'global_spec_bintian_8_shaft_cam_heald' => $request->global_spec_bintian_8_shaft_cam_heald,
            'global_spec_bintian_10_shaft_cam_heald' => $request->global_spec_bintian_10_shaft_cam_heald,
            'global_spec_changshu_16_shaft_2861_dobby' => $request->global_spec_changshu_16_shaft_2861_dobby,
            'global_spec_staubli_16_shaft_dobby' => $request->global_spec_staubli_16_shaft_dobby,
            'global_spec_increase_1_nozzle' => $request->global_spec_increase_1_nozzle,
            'global_spec_increase_1_feeder' => $request->global_spec_increase_1_feeder,
            'global_spec_reed_increase_per_10cm' => $request->global_spec_reed_increase_per_10cm,
            'global_spec_bintian_cam_plate_extra' => $request->global_spec_bintian_cam_plate_extra,
            'global_spec_bintian_gear_extra' => $request->global_spec_bintian_gear_extra,
            'global_difference_specification_in_print' => $request->has('global_difference_specification_in_print') ? (bool)$request->global_difference_specification_in_print : true,
            'global_difference_specification_extended_in_print' => $request->boolean('global_difference_specification_extended_in_print'),
            'global_spec_3_niupai_10_shaft_410_cam_heald_frames' => $request->global_spec_3_niupai_10_shaft_410_cam_heald_frames,
            'global_spec_3_niupai_12_shaft_411_cam_heald_frames' => $request->global_spec_3_niupai_12_shaft_411_cam_heald_frames,
            'global_spec_3_niupai_16_electronic_dobby_5400d_invertor' => $request->global_spec_3_niupai_16_electronic_dobby_5400d_invertor,
            'global_spec_3_sanhe_s650_controller_accessories' => $request->global_spec_3_sanhe_s650_controller_accessories,
            'global_spec_3_increase_1_colour' => $request->global_spec_3_increase_1_colour,
            'global_spec_3_increase_1_feeder' => $request->global_spec_3_increase_1_feeder,
            'global_spec_3_reed_increase_per_20cm' => $request->global_spec_3_reed_increase_per_20cm,
            'global_spec_3_single_pump_to_double_pump' => $request->global_spec_3_single_pump_to_double_pump,
            'global_difference_specification_3_in_print' => $request->boolean('global_difference_specification_3_in_print'),
            // Terms & conditions
            'global_terms_government_policies' => $request->global_terms_government_policies,
            'global_terms_currency' => $request->global_terms_currency,
            'global_terms_licenses_bank_payment' => $request->global_terms_licenses_bank_payment,
            'global_terms_demurrage_detentions' => $request->global_terms_demurrage_detentions,
            'global_terms_cancellation_order' => $request->global_terms_cancellation_order,
            'global_terms_jurisdiction_seller_rights' => $request->global_terms_jurisdiction_seller_rights,
            'global_terms_conditions_in_print' => $request->has('global_terms_conditions_in_print') ? (bool)$request->global_terms_conditions_in_print : true,
            'global_not_included_in_offer_in_print' => $request->has('global_not_included_in_offer_in_print') ? (bool) $request->global_not_included_in_offer_in_print : true,
            'global_not_included_in_offer' => Contract::notIncludedInOfferPayloadFromRequest($request, 'global_not_included_in_offer'),
        ]);

        return redirect()->route('settings.contract-details')->with('success', 'Global contract details updated successfully.');
    }

    /**
     * Show the PI Layouts settings page.
     */
    public function piLayouts()
    {
        $layouts = \App\Models\PILayout::withCount('sellers')->orderBy('is_default', 'desc')->orderBy('name')->get();
        return view('admin.pi-layouts-settings', compact('layouts'));
    }
}
