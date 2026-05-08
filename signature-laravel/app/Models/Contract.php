<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Contract extends Model
{
    protected $fillable = [
        'lead_id',
        'created_by',
        'business_firm_id',
        'contract_number',
        'buyer_name',
        'company_name',
        'contact_address',
        'state_id',
        'city_id',
        'area_id',
        'email',
        'phone_number',
        'phone_number_2',
        'gst',
        'pan',
        'total_amount',
        'token_amount',
        'machine_details',
        // Other Buyer Expenses Details
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
        // Other Details
        'payment_terms',
        'quote_validity',
        'loading_terms',
        'warranty',
        'complimentary_spares',
        'other_details_in_print',
        // Difference of Specification
        'spec_color_8_to_12_selectors',
        'spec_extra_feeder_per_pc',
        'spec_extra_warp_beam_per_pc',
        'spec_reed_reduction_per_20cm',
        'spec_reed_increase_per_20cm',
        'spec_increase_380_to_480cm',
        'spec_electronic_weft_cutter',
        'spec_hooks_5376_to_6144',
        'spec_hooks_5376_to_10240',
        'spec_hooks_5376_to_2688',
        'spec_changshu_to_sns_cam',
        'spec_changshu_to_sns_chain_24',
        'spec_changshu_to_sns_chain_16',
        'spec_changshu_to_jkd_or_changfang',
        'spec_changshu_to_wumu',
        'spec_bintian_8_shaft_cam_heald',
        'spec_bintian_10_shaft_cam_heald',
        'spec_changshu_16_shaft_2861_dobby',
        'spec_staubli_16_shaft_dobby',
        'spec_increase_1_nozzle',
        'spec_increase_1_feeder',
        'spec_reed_increase_per_10cm',
        'spec_bintian_cam_plate_extra',
        'spec_bintian_gear_extra',
        'difference_specification_in_print',
        'difference_specification_extended_in_print',
        'spec_3_niupai_10_shaft_410_cam_heald_frames',
        'spec_3_niupai_12_shaft_411_cam_heald_frames',
        'spec_3_niupai_16_electronic_dobby_5400d_invertor',
        'spec_3_sanhe_s650_controller_accessories',
        'spec_3_increase_1_colour',
        'spec_3_increase_1_feeder',
        'spec_3_reed_increase_per_20cm',
        'spec_3_single_pump_to_double_pump',
        'difference_specification_3_in_print',
        'terms_government_policies',
        'terms_currency',
        'terms_licenses_bank_payment',
        'terms_demurrage_detentions',
        'terms_cancellation_order',
        'terms_jurisdiction_seller_rights',
        'terms_conditions_in_print',
        'not_included_in_offer_in_print',
        'not_included_in_offer',
        'customer_signature',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'over_invoice_usd_inr_rate',
        'over_invoice_difference_inr',
    ];

    protected $casts = [
        'machine_details' => 'array',
        'other_buyer_expenses_in_print' => 'boolean',
        'other_details_in_print' => 'boolean',
        'difference_specification_in_print' => 'boolean',
        'difference_specification_extended_in_print' => 'boolean',
        'difference_specification_3_in_print' => 'boolean',
        'terms_conditions_in_print' => 'boolean',
        'not_included_in_offer_in_print' => 'boolean',
        'not_included_in_offer' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Merge stored JSON with defaults (all items default to checked / true).
     * Used for forms (old input) and display.
     *
     * @param  array<string, mixed>|null  $oldInput  Request old() array for not_included_in_offer.* keys
     * @param  array<string, mixed>|null  $storedJson  DB JSON column
     * @return array<string, bool>
     */
    public static function mergeNotIncludedInOfferFlags(?array $oldInput, $storedJson): array
    {
        $items = config('not_included_in_offer.items', []);
        $stored = is_array($storedJson) ? $storedJson : [];
        $out = [];
        foreach (array_keys($items) as $key) {
            if (is_array($oldInput) && array_key_exists($key, $oldInput)) {
                $out[$key] = filter_var($oldInput[$key], FILTER_VALIDATE_BOOLEAN);
            } elseif (array_key_exists($key, $stored)) {
                $out[$key] = (bool) $stored[$key];
            } else {
                $out[$key] = true;
            }
        }

        return $out;
    }

    /**
     * @return array<string, bool>
     */
    public static function notIncludedInOfferPayloadFromRequest(\Illuminate\Http\Request $request, string $prefix): array
    {
        $out = [];
        foreach (array_keys(config('not_included_in_offer.items', [])) as $key) {
            $out[$key] = $request->boolean($prefix.'.'.$key);
        }

        return $out;
    }

    /**
     * Checked item labels for PDF when section is set to Show.
     *
     * @return list<string>
     */
    public function notIncludedInOfferPdfLabels(): array
    {
        if (! $this->contractPrintFlag('not_included_in_offer_in_print', true)) {
            return [];
        }
        $labels = [];
        $flags = self::mergeNotIncludedInOfferFlags(null, $this->not_included_in_offer);
        foreach (config('not_included_in_offer.items', []) as $key => $label) {
            if (! empty($flags[$key])) {
                $labels[] = $label;
            }
        }

        return $labels;
    }

    /**
     * Feature flag: show "Other Buyer Expenses Details" in forms, contract show, and PDF.
     */
    public static function showOtherBuyerExpensesSection(): bool
    {
        return (bool) config('contract.show_other_buyer_expenses_section', false);
    }

    /**
     * Values to persist when creating a contract (request vs global defaults when section hidden).
     *
     * @return array<string, mixed>
     */
    public static function otherBuyerExpensesForStore(\Illuminate\Http\Request $request): array
    {
        if (self::showOtherBuyerExpensesSection()) {
            return [
                'overseas_freight' => $request->overseas_freight,
                'demurrage_detention_cfs_charges' => $request->demurrage_detention_cfs_charges,
                'air_pipe_connection' => $request->air_pipe_connection,
                'custom_duty' => $request->custom_duty,
                'port_expenses_transport' => $request->port_expenses_transport,
                'crane_foundation' => $request->crane_foundation,
                'humidification' => $request->humidification,
                'damage' => $request->damage,
                'gst_custom_charges' => $request->gst_custom_charges,
                'compressor' => $request->compressor,
                'optional_spares' => $request->optional_spares,
                'other_buyer_expenses_in_print' => $request->has('other_buyer_expenses_in_print') ? (bool) $request->other_buyer_expenses_in_print : true,
            ];
        }

        $g = Setting::firstOrCreate(['id' => 1]);

        return [
            'overseas_freight' => $g->global_overseas_freight,
            'demurrage_detention_cfs_charges' => $g->global_demurrage_detention_cfs_charges,
            'air_pipe_connection' => $g->global_air_pipe_connection,
            'custom_duty' => $g->global_custom_duty,
            'port_expenses_transport' => $g->global_port_expenses_transport,
            'crane_foundation' => $g->global_crane_foundation,
            'humidification' => $g->global_humidification,
            'damage' => $g->global_damage,
            'gst_custom_charges' => $g->global_gst_custom_charges,
            'compressor' => $g->global_compressor,
            'optional_spares' => $g->global_optional_spares,
            'other_buyer_expenses_in_print' => (bool) ($g->global_other_buyer_expenses_in_print ?? true),
        ];
    }

    /**
     * Values to persist when updating a contract (request vs keep existing when section hidden).
     *
     * @return array<string, mixed>
     */
    public static function otherBuyerExpensesForUpdate(\Illuminate\Http\Request $request, self $contract): array
    {
        if (self::showOtherBuyerExpensesSection()) {
            return [
                'overseas_freight' => $request->overseas_freight,
                'demurrage_detention_cfs_charges' => $request->demurrage_detention_cfs_charges,
                'air_pipe_connection' => $request->air_pipe_connection,
                'custom_duty' => $request->custom_duty,
                'port_expenses_transport' => $request->port_expenses_transport,
                'crane_foundation' => $request->crane_foundation,
                'humidification' => $request->humidification,
                'damage' => $request->damage,
                'gst_custom_charges' => $request->gst_custom_charges,
                'compressor' => $request->compressor,
                'optional_spares' => $request->optional_spares,
                'other_buyer_expenses_in_print' => $request->has('other_buyer_expenses_in_print') ? (bool) $request->other_buyer_expenses_in_print : $contract->other_buyer_expenses_in_print,
            ];
        }

        return [
            'overseas_freight' => $contract->overseas_freight,
            'demurrage_detention_cfs_charges' => $contract->demurrage_detention_cfs_charges,
            'air_pipe_connection' => $contract->air_pipe_connection,
            'custom_duty' => $contract->custom_duty,
            'port_expenses_transport' => $contract->port_expenses_transport,
            'crane_foundation' => $contract->crane_foundation,
            'humidification' => $contract->humidification,
            'damage' => $contract->damage,
            'gst_custom_charges' => $contract->gst_custom_charges,
            'compressor' => $contract->compressor,
            'optional_spares' => $contract->optional_spares,
            'other_buyer_expenses_in_print' => (bool) ($contract->other_buyer_expenses_in_print ?? true),
        ];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function businessFirm()
    {
        return $this->belongsTo(BusinessFirm::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function contractMachines()
    {
        return $this->hasMany(ContractMachine::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function machineStatus()
    {
        return $this->hasOne(MachineStatus::class);
    }

    public function proformaInvoices()
    {
        return $this->hasMany(ProformaInvoice::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Human labels for "Difference of Specification" fields (PDF / show view).
     *
     * @return array<string, string> attribute => label
     */
    public static function differenceSpecificationLabels(): array
    {
        return [
            'spec_color_8_to_12_selectors' => '8 Colour Selectors to 12 Colour Selectors',
            'spec_extra_feeder_per_pc' => 'Extra Feeder (Per PC)',
            'spec_extra_warp_beam_per_pc' => 'Extra Warp Beam (Per PC)',
            'spec_reed_reduction_per_20cm' => 'Reduction of Every 20cm in Reed Space',
            'spec_reed_increase_per_20cm' => 'Increase of Every 20cm in Reed Space',
            'spec_increase_380_to_480cm' => 'Increase from 380cm to 480cm',
            'spec_electronic_weft_cutter' => 'Electronic Weft Cutter',
            'spec_hooks_5376_to_6144' => '5376 Hooks to 6144 Hooks',
            'spec_hooks_5376_to_10240' => '5376 Hooks to 10240 Hooks',
            'spec_hooks_5376_to_2688' => '5376 Hooks to 2688 Hooks',
            'spec_changshu_to_sns_cam' => 'Changshu to SNS CAM',
            'spec_changshu_to_sns_chain_24' => 'Changshu to SNS Chain (24 Line)',
            'spec_changshu_to_sns_chain_16' => 'Changshu to SNS Chain (16 Line)',
            'spec_changshu_to_jkd_or_changfang' => 'Changshu to JKD or ChangFang',
            'spec_changshu_to_wumu' => 'Changshu to Wumu',
        ];
    }

    /**
     * Second "Difference of Specification" card (additional options).
     *
     * @return array<string, string> attribute => label
     */
    public static function differenceSpecificationExtendedLabels(): array
    {
        return [
            'spec_bintian_8_shaft_cam_heald' => '8 Shaft Bintian CAM with Heald Frames & I',
            'spec_bintian_10_shaft_cam_heald' => '10 Shaft Bintian CAM with Heald Frames',
            'spec_changshu_16_shaft_2861_dobby' => '16 Shaft Changshu 2861 Dobby',
            'spec_staubli_16_shaft_dobby' => '16 Shaft Staubli Dobby',
            'spec_increase_1_nozzle' => 'Increase 1 Nozzle',
            'spec_increase_1_feeder' => 'Increase 1 Feeder',
            'spec_reed_increase_per_10cm' => 'Increase of Every 10cm in Reed Space',
            'spec_bintian_cam_plate_extra' => 'Bintian CAM Plate (Extra)',
            'spec_bintian_gear_extra' => 'Bintian Gear (Extra)',
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function differenceSpecificationExtendedRows(): array
    {
        $rows = [];
        foreach (self::differenceSpecificationExtendedLabels() as $value => $label) {
            $rows[] = ['value' => $value, 'label' => $label];
        }

        return $rows;
    }

    /**
     * Third "Difference of Specification" card (Niupai / Sanhe options).
     *
     * @return array<string, string> attribute => label
     */
    public static function differenceSpecification3Labels(): array
    {
        return [
            'spec_3_niupai_10_shaft_410_cam_heald_frames' => '10 Shaft Niupai 410 CAM with Heald Frames',
            'spec_3_niupai_12_shaft_411_cam_heald_frames' => '12 Shaft Niupai 411 CAM with Heald Frames',
            'spec_3_niupai_16_electronic_dobby_5400d_invertor' => '16 Shaft Niupai Electronic Dobby with 5400D with Individual Invertor',
            'spec_3_sanhe_s650_controller_accessories' => 'Sanhe S650 Controller with Accessories',
            'spec_3_increase_1_colour' => 'Increase 1 Colour',
            'spec_3_increase_1_feeder' => 'Increase 1 Feeder',
            'spec_3_reed_increase_per_20cm' => 'Increase of Every 20cm in Reed Space',
            'spec_3_single_pump_to_double_pump' => 'Single Pump to Double Pump',
        ];
    }

    public function hasDifferenceSpecification3Content(): bool
    {
        foreach (array_keys(self::differenceSpecification3Labels()) as $field) {
            if (filled($this->{$field})) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<array{label: string, value: mixed}>
     */
    public function differenceSpecification3PrintRows(): array
    {
        if (! $this->contractPrintFlag('difference_specification_3_in_print', false)) {
            return [];
        }
        $rows = [];
        foreach (self::differenceSpecification3Labels() as $field => $label) {
            if (filled($this->{$field})) {
                $rows[] = ['label' => $label, 'value' => $this->{$field}];
            }
        }

        return $rows;
    }

    /**
     * First Difference of Specification card (screen): section print on and any main field filled.
     */
    public function hasDifferenceSpecificationContent(): bool
    {
        if (!$this->difference_specification_in_print) {
            return false;
        }
        foreach (array_keys(self::differenceSpecificationLabels()) as $field) {
            if (filled($this->{$field})) {
                return true;
            }
        }

        return false;
    }

    /**
     * Second Difference of Specification card (screen): any additional field filled.
     */
    public function hasDifferenceSpecificationExtendedContent(): bool
    {
        foreach (array_keys(self::differenceSpecificationExtendedLabels()) as $field) {
            if (filled($this->{$field})) {
                return true;
            }
        }

        return false;
    }

    /**
     * In Print (PDF): true only when the contract flag is explicitly on (Show).
     * Uses raw attributes so 0/1 from the DB always match the form Hide/Show.
     *
     * @param  bool  $defaultWhenNull  when the column is missing or null (legacy rows)
     */
    protected function contractPrintFlag(string $attribute, bool $defaultWhenNull): bool
    {
        if (! array_key_exists($attribute, $this->getAttributes())) {
            return $defaultWhenNull;
        }
        $raw = $this->getAttributes()[$attribute];
        if ($raw === null) {
            return $defaultWhenNull;
        }

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Rows for PDF "Other buyer expenses" (empty when In Print = Hide or no values).
     *
     * @return list<array{label: string, value: mixed}>
     */
    public function otherBuyerExpensesPdfRows(): array
    {
        if (! self::showOtherBuyerExpensesSection()) {
            return [];
        }
        if (! $this->contractPrintFlag('other_buyer_expenses_in_print', true)) {
            return [];
        }
        $rows = [];
        if (filled($this->overseas_freight)) {
            $rows[] = ['label' => 'Overseas Freight', 'value' => $this->overseas_freight];
        }
        if (filled($this->demurrage_detention_cfs_charges)) {
            $rows[] = ['label' => 'Demurrage / Detention / CFS Charges', 'value' => $this->demurrage_detention_cfs_charges];
        }
        if (filled($this->air_pipe_connection)) {
            $rows[] = ['label' => 'Air Pipe Connection', 'value' => $this->air_pipe_connection];
        }
        if (filled($this->custom_duty)) {
            $rows[] = ['label' => 'Custom Duty', 'value' => $this->custom_duty];
        }
        if (filled($this->port_expenses_transport)) {
            $rows[] = ['label' => 'Port Expenses & Transport', 'value' => $this->port_expenses_transport];
        }
        if (filled($this->crane_foundation)) {
            $rows[] = ['label' => 'Crane & Foundation', 'value' => $this->crane_foundation];
        }
        if (filled($this->humidification)) {
            $rows[] = ['label' => 'Humidification', 'value' => $this->humidification];
        }
        if (filled($this->damage)) {
            $rows[] = ['label' => 'Damage', 'value' => $this->damage];
        }
        if (filled($this->gst_custom_charges)) {
            $rows[] = ['label' => 'GST & Custom Charges', 'value' => $this->gst_custom_charges];
        }
        if (filled($this->compressor)) {
            $rows[] = ['label' => 'Compressor', 'value' => $this->compressor];
        }
        if (filled($this->optional_spares)) {
            $rows[] = ['label' => 'Optional Spares', 'value' => $this->optional_spares];
        }

        return $rows;
    }

    /**
     * Rows for PDF "Other details" (empty when In Print = Hide or no values).
     *
     * @return list<array{label: string, value: mixed}>
     */
    public function otherDetailsPdfRows(): array
    {
        if (! $this->contractPrintFlag('other_details_in_print', true)) {
            return [];
        }
        $rows = [];
        if (filled($this->payment_terms)) {
            $rows[] = ['label' => 'Payment Terms', 'value' => $this->payment_terms];
        }
        if (filled($this->quote_validity)) {
            $rows[] = ['label' => 'Quote Validity', 'value' => $this->quote_validity];
        }
        if (filled($this->loading_terms)) {
            $rows[] = ['label' => 'Loading Terms', 'value' => $this->loading_terms];
        }
        if (filled($this->warranty)) {
            $rows[] = ['label' => 'Warranty', 'value' => $this->warranty];
        }
        if (filled($this->complimentary_spares)) {
            $rows[] = ['label' => 'Complimentary Spares', 'value' => $this->complimentary_spares];
        }

        return $rows;
    }

    /**
     * @return list<array{label: string, value: mixed}>
     */
    public function differenceSpecificationMainPrintRows(): array
    {
        if (! $this->contractPrintFlag('difference_specification_in_print', true)) {
            return [];
        }
        $rows = [];
        foreach (self::differenceSpecificationLabels() as $field => $label) {
            if (filled($this->{$field})) {
                $rows[] = ['label' => $label, 'value' => $this->{$field}];
            }
        }

        return $rows;
    }

    /**
     * @return list<array{label: string, value: mixed}>
     */
    public function differenceSpecificationExtendedPrintRows(): array
    {
        if (! $this->contractPrintFlag('difference_specification_extended_in_print', false)) {
            return [];
        }
        $rows = [];
        foreach (self::differenceSpecificationExtendedLabels() as $field => $label) {
            if (filled($this->{$field})) {
                $rows[] = ['label' => $label, 'value' => $this->{$field}];
            }
        }

        return $rows;
    }

    /**
     * Long-form terms & conditions blocks (aligned with global contract settings labels).
     *
     * @return array<string, string> attribute => label
     */
    public static function termsConditionsLabels(): array
    {
        return [
            'terms_government_policies' => 'Government Policies',
            'terms_currency' => 'Currency',
            'terms_licenses_bank_payment' => 'Licenses & Bank Payment',
            'terms_demurrage_detentions' => 'Demurrage & Detentions',
            'terms_cancellation_order' => 'Cancellation of Order',
            'terms_jurisdiction_seller_rights' => 'Jurisdiction & Seller Rights',
        ];
    }

    public function hasTermsConditionsContent(): bool
    {
        foreach (array_keys(self::termsConditionsLabels()) as $field) {
            if (filled($this->{$field})) {
                return true;
            }
        }

        return false;
    }

    /**
     * Terms blocks for PDF only when In Print = Show and at least one block has text.
     *
     * @return list<array{label: string, body: string}>
     */
    public function termsConditionsPdfBlocks(): array
    {
        if (! $this->contractPrintFlag('terms_conditions_in_print', true)) {
            return [];
        }
        $blocks = [];
        foreach (self::termsConditionsLabels() as $field => $label) {
            if (filled($this->{$field})) {
                $blocks[] = ['label' => $label, 'body' => (string) $this->{$field}];
            }
        }

        return $blocks;
    }
}
