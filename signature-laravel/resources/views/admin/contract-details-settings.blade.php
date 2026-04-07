<x-app-layout>
    <div class="mb-4">
        <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Global Contract Details Settings</h1>
        <p class="text-muted mb-0">Set default values for all contracts. These values will be used when creating new contracts.</p>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Global Default Values</h2>
                    </div>

                    <form action="{{ route('settings.update-contract-details') }}" method="POST">
                        @csrf

                        <!-- Other Buyer Expenses Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Buyer Expenses Details</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_other_buyer_expenses_in_print" id="global_buyer_expenses_show" value="1" {{ $setting->global_other_buyer_expenses_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_buyer_expenses_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_other_buyer_expenses_in_print" id="global_buyer_expenses_hide" value="0" {{ !$setting->global_other_buyer_expenses_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_buyer_expenses_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Overseas Freight</label>
                                                <input type="text" name="global_overseas_freight" value="{{ old('global_overseas_freight', $setting->global_overseas_freight ?? 'CHA will provide') }}" 
                                                       class="form-control" placeholder="CHA will provide" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Demurrage / Detention / CFS Charges</label>
                                                <input type="text" name="global_demurrage_detention_cfs_charges" value="{{ old('global_demurrage_detention_cfs_charges', $setting->global_demurrage_detention_cfs_charges ?? 'At Actual') }}" 
                                                       class="form-control" placeholder="At Actual" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Air Pipe Connection</label>
                                                <input type="text" name="global_air_pipe_connection" value="{{ old('global_air_pipe_connection', $setting->global_air_pipe_connection) }}" 
                                                       class="form-control" placeholder="Enter air pipe connection" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Custom Duty</label>
                                                <input type="text" name="global_custom_duty" value="{{ old('global_custom_duty', $setting->global_custom_duty) }}" 
                                                       class="form-control" placeholder="Enter custom duty" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Port Expenses & Transport</label>
                                                <input type="text" name="global_port_expenses_transport" value="{{ old('global_port_expenses_transport', $setting->global_port_expenses_transport ?? 'CHA will provide') }}" 
                                                       class="form-control" placeholder="CHA will provide" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Crane & Foundation</label>
                                                <input type="text" name="global_crane_foundation" value="{{ old('global_crane_foundation', $setting->global_crane_foundation ?? 'By Buyer') }}" 
                                                       class="form-control" placeholder="By Buyer" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Humidification</label>
                                                <input type="text" name="global_humidification" value="{{ old('global_humidification', $setting->global_humidification) }}" 
                                                       class="form-control" placeholder="Enter humidification" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Damage</label>
                                                <input type="text" name="global_damage" value="{{ old('global_damage', $setting->global_damage) }}" 
                                                       class="form-control" placeholder="Enter damage" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">GST & Custom Charges</label>
                                                <input type="text" name="global_gst_custom_charges" value="{{ old('global_gst_custom_charges', $setting->global_gst_custom_charges ?? 'At Actual By Buyer') }}" 
                                                       class="form-control" placeholder="At Actual By Buyer" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Compressor</label>
                                                <input type="text" name="global_compressor" value="{{ old('global_compressor', $setting->global_compressor) }}" 
                                                       class="form-control" placeholder="Enter compressor" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Optional Spares</label>
                                                <input type="text" name="global_optional_spares" value="{{ old('global_optional_spares', $setting->global_optional_spares) }}" 
                                                       class="form-control" placeholder="Enter optional spares" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Details</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_other_details_in_print" id="global_other_details_show" value="1" {{ $setting->global_other_details_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_other_details_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_other_details_in_print" id="global_other_details_hide" value="0" {{ !$setting->global_other_details_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_other_details_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Payment Terms</label>
                                                <input type="text" name="global_payment_terms" value="{{ old('global_payment_terms', $setting->global_payment_terms ?? '10% Token + 15% Advance + 75% Before Shipment') }}" 
                                                       class="form-control" placeholder="Enter payment terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Quote Validity</label>
                                                <input type="text" name="global_quote_validity" value="{{ old('global_quote_validity', $setting->global_quote_validity ?? '10 Days') }}" 
                                                       class="form-control" placeholder="Enter quote validity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Loading Terms</label>
                                                <input type="text" name="global_loading_terms" value="{{ old('global_loading_terms', $setting->global_loading_terms ?? '30 Days from 100% Payment') }}" 
                                                       class="form-control" placeholder="Enter loading terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Warranty</label>
                                                <input type="text" name="global_warranty" value="{{ old('global_warranty', $setting->global_warranty ?? '1 Year from Date of Loading') }}" 
                                                       class="form-control" placeholder="Enter warranty" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Complimentary Spares</label>
                                                <input type="text" name="global_complimentary_spares" value="{{ old('global_complimentary_spares', $setting->global_complimentary_spares ?? 'As per list attached') }}" 
                                                       class="form-control" placeholder="Enter complimentary spares" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_difference_specification_in_print" id="global_specification_show" value="1" {{ $setting->global_difference_specification_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_specification_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_difference_specification_in_print" id="global_specification_hide" value="0" {{ !$setting->global_difference_specification_in_print ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_specification_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">8 Colour Selectors to 12 Colour Selectors</label>
                                                <input type="text" name="global_spec_color_8_to_12_selectors" value="{{ old('global_spec_color_8_to_12_selectors', $setting->global_spec_color_8_to_12_selectors) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Feeder (Per PC)</label>
                                                <input type="text" name="global_spec_extra_feeder_per_pc" value="{{ old('global_spec_extra_feeder_per_pc', $setting->global_spec_extra_feeder_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Warp Beam (Per PC)</label>
                                                <input type="text" name="global_spec_extra_warp_beam_per_pc" value="{{ old('global_spec_extra_warp_beam_per_pc', $setting->global_spec_extra_warp_beam_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Reduction of Every 20cm in Reed Space</label>
                                                <input type="text" name="global_spec_reed_reduction_per_20cm" value="{{ old('global_spec_reed_reduction_per_20cm', $setting->global_spec_reed_reduction_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase of Every 20cm in Reed Space</label>
                                                <input type="text" name="global_spec_reed_increase_per_20cm" value="{{ old('global_spec_reed_increase_per_20cm', $setting->global_spec_reed_increase_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase from 380cm to 480cm</label>
                                                <input type="text" name="global_spec_increase_380_to_480cm" value="{{ old('global_spec_increase_380_to_480cm', $setting->global_spec_increase_380_to_480cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Electronic Weft Cutter</label>
                                                <input type="text" name="global_spec_electronic_weft_cutter" value="{{ old('global_spec_electronic_weft_cutter', $setting->global_spec_electronic_weft_cutter) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 6144 Hooks</label>
                                                <input type="text" name="global_spec_hooks_5376_to_6144" value="{{ old('global_spec_hooks_5376_to_6144', $setting->global_spec_hooks_5376_to_6144) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 10240 Hooks</label>
                                                <input type="text" name="global_spec_hooks_5376_to_10240" value="{{ old('global_spec_hooks_5376_to_10240', $setting->global_spec_hooks_5376_to_10240) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 2688 Hooks</label>
                                                <input type="text" name="global_spec_hooks_5376_to_2688" value="{{ old('global_spec_hooks_5376_to_2688', $setting->global_spec_hooks_5376_to_2688) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS CAM</label>
                                                <input type="text" name="global_spec_changshu_to_sns_cam" value="{{ old('global_spec_changshu_to_sns_cam', $setting->global_spec_changshu_to_sns_cam) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (24 Line)</label>
                                                <input type="text" name="global_spec_changshu_to_sns_chain_24" value="{{ old('global_spec_changshu_to_sns_chain_24', $setting->global_spec_changshu_to_sns_chain_24) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (16 Line)</label>
                                                <input type="text" name="global_spec_changshu_to_sns_chain_16" value="{{ old('global_spec_changshu_to_sns_chain_16', $setting->global_spec_changshu_to_sns_chain_16) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to JKD or ChangFang</label>
                                                <input type="text" name="global_spec_changshu_to_jkd_or_changfang" value="{{ old('global_spec_changshu_to_jkd_or_changfang', $setting->global_spec_changshu_to_jkd_or_changfang) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to Wumu</label>
                                                <input type="text" name="global_spec_changshu_to_wumu" value="{{ old('global_spec_changshu_to_wumu', $setting->global_spec_changshu_to_wumu) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification (Additional) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Additional)</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_difference_specification_extended_in_print" id="global_spec_ext_show" value="1" {{ ($setting->global_difference_specification_extended_in_print ?? false) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_spec_ext_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_difference_specification_extended_in_print" id="global_spec_ext_hide" value="0" {{ !($setting->global_difference_specification_extended_in_print ?? false) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_spec_ext_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            @foreach (\App\Models\Contract::differenceSpecificationExtendedLabels() as $field => $label)
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">{{ $label }}</label>
                                                <input type="text" name="global_{{ $field }}" value="{{ old('global_' . $field, $setting->{'global_' . $field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification 3 -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification 3</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_difference_specification_3_in_print" id="global_spec_3_show" value="1" {{ ($setting->global_difference_specification_3_in_print ?? false) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_spec_3_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_difference_specification_3_in_print" id="global_spec_3_hide" value="0" {{ !($setting->global_difference_specification_3_in_print ?? false) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_spec_3_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            @foreach (\App\Models\Contract::differenceSpecification3Labels() as $field => $label)
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">{{ $label }}</label>
                                                <input type="text" name="global_{{ $field }}" value="{{ old('global_' . $field, $setting->{'global_' . $field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & conditions Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Terms &amp; conditions</h5>
                                            <div class="d-flex align-items-center">
                                                <label class="form-label fw-medium mb-0 me-3" style="color: #374151;">In Print (Default) :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="global_terms_conditions_in_print" id="global_terms_conditions_show" value="1" {{ ($setting->global_terms_conditions_in_print ?? true) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="global_terms_conditions_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="global_terms_conditions_in_print" id="global_terms_conditions_hide" value="0" {{ !($setting->global_terms_conditions_in_print ?? true) ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="global_terms_conditions_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Government Policies</label>
                                                <textarea name="global_terms_government_policies" rows="4" class="form-control" placeholder="Enter government policies" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_government_policies', $setting->global_terms_government_policies) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Currency</label>
                                                <textarea name="global_terms_currency" rows="4" class="form-control" placeholder="Enter currency terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_currency', $setting->global_terms_currency) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Licenses &amp; Bank Payment</label>
                                                <textarea name="global_terms_licenses_bank_payment" rows="4" class="form-control" placeholder="Enter licenses &amp; bank payment" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_licenses_bank_payment', $setting->global_terms_licenses_bank_payment) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Demurrage &amp; Detentions</label>
                                                <textarea name="global_terms_demurrage_detentions" rows="4" class="form-control" placeholder="Enter demurrage &amp; detentions" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_demurrage_detentions', $setting->global_terms_demurrage_detentions) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Cancellation of Order</label>
                                                <textarea name="global_terms_cancellation_order" rows="4" class="form-control" placeholder="Enter cancellation of order terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_cancellation_order', $setting->global_terms_cancellation_order) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Jurisdiction &amp; Seller Rights</label>
                                                <textarea name="global_terms_jurisdiction_seller_rights" rows="4" class="form-control" placeholder="Enter jurisdiction &amp; seller rights" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('global_terms_jurisdiction_seller_rights', $setting->global_terms_jurisdiction_seller_rights) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                    <i class="fas fa-save me-2"></i>Save Global Contract Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>
