<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Contract Details</h1>
            <p class="text-muted mb-0">Contract: {{ $contract->contract_number }}</p>
        </div>
        <div class="d-flex gap-2">
            @canany(['view contract approvals', 'convert contract'])
            <a href="{{ route('machine-statuses.create', ['contract_id' => $contract->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks me-2"></i>Status
            </a>
            @endcanany
            <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Contracts
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Contract Information Card -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                            <div class="fw-semibold" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Business Firm</label>
                            <div style="color: #1f2937;">{{ $contract->businessFirm->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Buyer Name</label>
                            <div class="fw-medium" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Company Name</label>
                            <div style="color: #1f2937;">{{ $contract->company_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Phone Number</label>
                            <div style="color: #1f2937;">{{ $contract->phone_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Email</label>
                            <div style="color: #1f2937;">{{ $contract->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Location</label>
                            <div style="color: #1f2937;">{{ $contract->area->name ?? '' }}, {{ $contract->city->name ?? '' }}, {{ $contract->state->name ?? '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Amount</label>
                            <div class="fw-semibold text-primary" style="font-size: 1.125rem;">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                        </div>
                        @if($contract->token_amount)
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Token Amount</label>
                            <div class="fw-semibold" style="color: #059669; font-size: 1.125rem;">₹{{ number_format($contract->token_amount, 2) }}</div>
                        </div>
                        @endif
                        @if($contract->creator)
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Created By</label>
                            <div style="color: #1f2937;">{{ $contract->creator->name }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Approval Status</label>
                            <div>
                                @if($contract->approval_status === 'pending')
                                    @if($contract->customer_signature)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="fas fa-pen me-1"></i>Awaiting Signature
                                        </span>
                                    @endif
                                @elseif($contract->approval_status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Approved
                                    </span>
                                    @if($contract->approver)
                                        <small class="text-muted ms-2">by {{ $contract->approver->name }}</small>
                                    @endif
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Rejected
                                    </span>
                                    @if($contract->approver)
                                        <small class="text-muted ms-2">by {{ $contract->approver->name }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Machine Details -->
                    @if($contract->contractMachines->count() > 0)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Machine Details</h5>
                        @foreach($contract->contractMachines as $index => $machine)
                            <div class="card mb-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0" style="color: #1f2937;">
                                            Machine #{{ $index + 1 }}
                                            @if($machine->machineCategory)
                                                - {{ $machine->machineCategory->name }}
                                            @endif
                                        </h6>
                                        <div class="fw-semibold text-primary">${{ number_format($machine->quantity * $machine->amount, 2) }}</div>
                                    </div>
                                    <div class="row g-2 small">
                                        @if($machine->brand)
                                        <div class="col-md-6"><strong>Brand:</strong> {{ $machine->brand->name }}</div>
                                        @endif
                                        @if($machine->seller)
                                        <div class="col-md-6"><strong>Machine Seller:</strong> {{ $machine->seller->seller_name }}</div>
                                        @endif
                                        @if($machine->machineModel)
                                        <div class="col-md-6"><strong>Model:</strong> {{ $machine->machineModel->model_no }}</div>
                                        @endif
                                        @if($machine->feeder)
                                        <div class="col-md-6"><strong>Feeder:</strong> {{ $machine->feeder->feeder }}{{ $machine->feeder->feederBrand ? ' (' . $machine->feeder->feederBrand->name . ')' : '' }}</div>
                                        @endif
                                        @if($machine->color)
                                        <div class="col-md-6"><strong>Color Selector:</strong> {{ $machine->color->name }}</div>
                                        @endif
                                        @if($machine->machineDropin)
                                        <div class="col-md-6"><strong>Dropins:</strong> {{ $machine->machineDropin->name }}</div>
                                        @endif
                                        @if($machine->machineBeam)
                                        <div class="col-md-6"><strong>Beam:</strong> {{ $machine->machineBeam->name }}</div>
                                        @endif
                                        @if(!empty($machine->machine_size_name))
                                        <div class="col-md-6"><strong>Machine Size:</strong> {{ $machine->machine_size_name }}</div>
                                        @endif
                                        @if($machine->machineClothRoller)
                                        <div class="col-md-6"><strong>Cloth Roller:</strong> {{ $machine->machineClothRoller->name }}</div>
                                        @endif
                                        @if($machine->machineHook)
                                        <div class="col-md-6"><strong>Hooks:</strong> {{ $machine->machineHook->hook }}</div>
                                        @endif
                                        @if($machine->machineERead)
                                        <div class="col-md-6"><strong>E-Read:</strong> {{ $machine->machineERead->name }}</div>
                                        @endif
                                        @if($machine->machineNozzle)
                                        <div class="col-md-6"><strong>Nozzle:</strong> {{ $machine->machineNozzle->nozzle }}</div>
                                        @endif
                                        @if($machine->machineSoftware)
                                        <div class="col-md-6"><strong>Software:</strong> {{ $machine->machineSoftware->name }}</div>
                                        @endif
                                        @if($machine->hsnCode)
                                        <div class="col-md-6"><strong>HSN Code:</strong> {{ $machine->hsnCode->hsn_code }}</div>
                                        @endif
                                        @if($machine->wir)
                                        <div class="col-md-6"><strong>WIR:</strong> {{ $machine->wir->wir }}</div>
                                        @endif
                                        @if($machine->machineShaft)
                                        <div class="col-md-6"><strong>Shaft:</strong> {{ $machine->machineShaft->name }}</div>
                                        @endif
                                        @if($machine->machineLever)
                                        <div class="col-md-6"><strong>Lever:</strong> {{ $machine->machineLever->name }}</div>
                                        @endif
                                        @if($machine->machineChain)
                                        <div class="col-md-6"><strong>Chain:</strong> {{ $machine->machineChain->name }}</div>
                                        @endif
                                        @if($machine->machineHealdWire)
                                        <div class="col-md-6"><strong>Heald Wires:</strong> {{ $machine->machineHealdWire->name }}</div>
                                        @endif
                                        <div class="col-md-6"><strong>Quantity:</strong> {{ $machine->quantity }}</div>
                                        <div class="col-md-6"><strong>Price:</strong> ${{ number_format($machine->amount, 2) }}</div>
                                        @if($machine->deliveryTerm)
                                        <div class="col-md-6"><strong>Delivery Terms:</strong> {{ $machine->deliveryTerm->name }}</div>
                                        @endif
                                        @if($machine->description)
                                        <div class="col-md-12"><strong>Description:</strong> {{ $machine->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Other Buyer Expenses Details -->
                    @if(\App\Models\Contract::showOtherBuyerExpensesSection() && $contract->other_buyer_expenses_in_print && ($contract->overseas_freight || $contract->demurrage_detention_cfs_charges || $contract->air_pipe_connection || $contract->custom_duty || $contract->port_expenses_transport || $contract->crane_foundation || $contract->humidification || $contract->damage || $contract->gst_custom_charges || $contract->compressor || $contract->optional_spares))
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Other Buyer Expenses Details</h5>
                        <div class="row g-3">
                            @if($contract->overseas_freight)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Overseas Freight</label>
                                <div style="color: #1f2937;">{{ $contract->overseas_freight }}</div>
                            </div>
                            @endif
                            @if($contract->demurrage_detention_cfs_charges)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Demurrage / Detention / CFS Charges</label>
                                <div style="color: #1f2937;">{{ $contract->demurrage_detention_cfs_charges }}</div>
                            </div>
                            @endif
                            @if($contract->air_pipe_connection)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Air Pipe Connection</label>
                                <div style="color: #1f2937;">{{ $contract->air_pipe_connection }}</div>
                            </div>
                            @endif
                            @if($contract->custom_duty)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Custom Duty</label>
                                <div style="color: #1f2937;">{{ $contract->custom_duty }}</div>
                            </div>
                            @endif
                            @if($contract->port_expenses_transport)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Port Expenses & Transport</label>
                                <div style="color: #1f2937;">{{ $contract->port_expenses_transport }}</div>
                            </div>
                            @endif
                            @if($contract->crane_foundation)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Crane & Foundation</label>
                                <div style="color: #1f2937;">{{ $contract->crane_foundation }}</div>
                            </div>
                            @endif
                            @if($contract->humidification)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Humidification</label>
                                <div style="color: #1f2937;">{{ $contract->humidification }}</div>
                            </div>
                            @endif
                            @if($contract->damage)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Damage</label>
                                <div style="color: #1f2937;">{{ $contract->damage }}</div>
                            </div>
                            @endif
                            @if($contract->gst_custom_charges)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">GST & Custom Charges</label>
                                <div style="color: #1f2937;">{{ $contract->gst_custom_charges }}</div>
                            </div>
                            @endif
                            @if($contract->compressor)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Compressor</label>
                                <div style="color: #1f2937;">{{ $contract->compressor }}</div>
                            </div>
                            @endif
                            @if($contract->optional_spares)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Optional Spares</label>
                                <div style="color: #1f2937;">{{ $contract->optional_spares }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Difference of Specification -->
                    @if($contract->hasDifferenceSpecificationContent())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Difference of Specification (Rapier - Jacquard)</h5>
                        <div class="row g-3">
                            @foreach(\App\Models\Contract::differenceSpecificationLabels() as $field => $label)
                                @if(filled($contract->{$field}))
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">{{ $label }}</label>
                                    <div style="color: #1f2937;">{{ $contract->{$field} }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Difference of Specification (Airjet) -->
                    @if($contract->hasDifferenceSpecificationExtendedContent())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Difference of Specification (Airjet)</h5>
                        <div class="row g-3">
                            @foreach(\App\Models\Contract::differenceSpecificationExtendedLabels() as $field => $label)
                                @if(filled($contract->{$field}))
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">{{ $label }}</label>
                                    <div style="color: #1f2937;">{{ $contract->{$field} }}</div>
                                    @if(!($contract->difference_specification_extended_in_print ?? false))
                                    <small class="text-muted">Hidden from print (PDF)</small>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Difference of Specification (Waterjet) -->
                    @if($contract->hasDifferenceSpecification3Content())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Difference of Specification (Waterjet)</h5>
                        <div class="row g-3">
                            @foreach(\App\Models\Contract::differenceSpecification3Labels() as $field => $label)
                                @if(filled($contract->{$field}))
                                <div class="col-md-6">
                                    <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">{{ $label }}</label>
                                    <div style="color: #1f2937;">{{ $contract->{$field} }}</div>
                                    @if(!($contract->difference_specification_3_in_print ?? false))
                                    <small class="text-muted">Hidden from print (PDF)</small>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Terms & conditions -->
                    @if($contract->hasTermsConditionsContent())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Terms &amp; conditions</h5>
                        <div class="row g-3">
                            @foreach(\App\Models\Contract::termsConditionsLabels() as $field => $label)
                                @if(filled($contract->{$field}))
                                <div class="col-12">
                                    <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">{{ $label }}</label>
                                    <div style="color: #1f2937; white-space: pre-wrap;">{{ $contract->{$field} }}</div>
                                    @if(!($contract->terms_conditions_in_print ?? true))
                                    <small class="text-muted">Hidden from print (PDF)</small>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Other Details -->
                    @if($contract->payment_terms || $contract->quote_validity || $contract->loading_terms || $contract->warranty || $contract->complimentary_spares)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Other Details</h5>
                        <div class="row g-3">
                            @if($contract->payment_terms)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Payment Terms</label>
                                <div style="color: #1f2937;">{{ $contract->payment_terms }}</div>
                            </div>
                            @endif
                            @if($contract->quote_validity)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Quote Validity</label>
                                <div style="color: #1f2937;">{{ $contract->quote_validity }}</div>
                            </div>
                            @endif
                            @if($contract->loading_terms)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Loading Terms</label>
                                <div style="color: #1f2937;">{{ $contract->loading_terms }}</div>
                            </div>
                            @endif
                            @if($contract->warranty)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Warranty</label>
                                <div style="color: #1f2937;">{{ $contract->warranty }}</div>
                            </div>
                            @endif
                            @if($contract->complimentary_spares)
                            <div class="col-md-6">
                                <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Complimentary Spares</label>
                                <div style="color: #1f2937;">{{ $contract->complimentary_spares }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Not Included in Offer -->
                    @php
                        $nioShow = \App\Models\Contract::mergeNotIncludedInOfferFlags(null, $contract->not_included_in_offer);
                        $nioAny = in_array(true, $nioShow, true);
                    @endphp
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Not Included in Offer</h5>
                        @if(!($contract->not_included_in_offer_in_print ?? true))
                            <p class="small text-muted mb-2">Hidden from print (PDF)</p>
                        @endif
                        @if($nioAny)
                            <ul class="list-unstyled row g-2 mb-0">
                                @foreach(config('not_included_in_offer.items', []) as $key => $label)
                                    @if(!empty($nioShow[$key]))
                                        <li class="col-md-6"><i class="fas fa-check text-success me-2"></i>{{ $label }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted small mb-0">No items selected.</p>
                        @endif
                    </div>

                    <!-- Complaints (for this contract / client) -->
                    @can('view complain')
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">
                                <i class="fas fa-exclamation-triangle me-2" style="color: var(--primary-color);"></i>Complaints
                            </h5>
                            @can('create complain')
                            <a href="{{ route('complaints.create', ['contract_id' => $contract->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Complain
                            </a>
                            @endcan
                        </div>
                        @if($contract->complaints->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="border: 1px solid #e5e7eb;">
                                    <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                        <tr>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">#</th>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">Type</th>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">Machine Category</th>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">Khata / Serial</th>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">Date</th>
                                            <th class="border-0 py-2 px-2 fw-medium" style="color: var(--primary-color); font-size: 0.8rem;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contract->complaints as $complaint)
                                        <tr>
                                            <td class="py-2 px-2">{{ $loop->iteration }}</td>
                                            <td class="py-2 px-2">
                                                @if($complaint->complainType)
                                                <span class="badge rounded-pill" style="background: color-mix(in srgb, var(--primary-color) 20%, #fff); color: var(--primary-color);">{{ $complaint->complainType->name }}</span>
                                                @else — @endif
                                            </td>
                                            <td class="py-2 px-2">{{ $complaint->machineCategory->name ?? '—' }}</td>
                                            <td class="py-2 px-2">{{ $complaint->machine_khata_number ?: '—' }}</td>
                                            <td class="py-2 px-2">{{ $complaint->created_at->format('d M Y') }}</td>
                                            <td class="py-2 px-2">
                                                <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0 small">No complaints for this contract.</p>
                        @endif
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Signatures Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-signature text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Signatures</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Creator Signature -->
                    <div class="mb-4">
                        <label class="form-label fw-medium mb-2" style="color: #374151;">
                            <i class="fas fa-user-plus me-2"></i>Created By Signature
                        </label>
                        @if($contract->creator && !empty($contract->creator->signature))
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                @php
                                    $signatureExists = Storage::disk('public')->exists($contract->creator->signature);
                                @endphp
                                @if($signatureExists)
                                    <img src="{{ Storage::url($contract->creator->signature) }}" 
                                         alt="Creator Signature" 
                                         class="img-fluid" 
                                         style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                @else
                                    <div>
                                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                        <p class="text-muted mt-2 mb-0 small">Signature file not found</p>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">Created by: {{ $contract->creator->name }}</small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">No signature available</p>
                                </div>
                            </div>
                            @if($contract->creator)
                                <small class="text-muted">Created by: {{ $contract->creator->name }}</small>
                            @endif
                        @endif
                    </div>

                    <!-- Approver Signature -->
                    @if($contract->approval_status === 'approved' && $contract->approver)
                    <div class="mb-4">
                        <label class="form-label fw-medium mb-2" style="color: #374151;">
                            <i class="fas fa-check-circle me-2"></i>Approved By Signature
                        </label>
                        @if($contract->approver->signature)
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ Storage::url($contract->approver->signature) }}" 
                                     alt="Approver Signature" 
                                     class="img-fluid" 
                                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
                            </div>
                            <small class="text-muted">
                                Approved by: {{ $contract->approver->name }}
                                @if($contract->approved_at)
                                    on {{ $contract->approved_at->format('M d, Y') }}
                                @endif
                            </small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">No signature available</p>
                                </div>
                            </div>
                            <small class="text-muted">
                                Approved by: {{ $contract->approver->name }}
                                @if($contract->approved_at)
                                    on {{ $contract->approved_at->format('M d, Y') }}
                                @endif
                            </small>
                        @endif
                    </div>
                    @endif

                    <!-- Customer Signature -->
                    <div class="mb-4">
                        <label class="form-label fw-medium mb-2" style="color: #374151;">
                            <i class="fas fa-user me-2"></i>Customer Signature
                        </label>
                        @if($contract->customer_signature)
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: white; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $contract->customer_signature }}" 
                                     alt="Customer Signature" 
                                     class="img-fluid" 
                                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
                            </div>
                            <small class="text-muted">
                                @if($contract->approval_status === 'pending')
                                    <span class="badge bg-warning">Pending Approval</span>
                                @elseif($contract->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </small>
                        @else
                            <div class="border rounded p-3 text-center" style="border-color: #e5e7eb !important; background: #f9fafb; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-signature text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0 small">Not signed yet</p>
                                </div>
                            </div>
                            <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-info mt-2">
                                <i class="fas fa-pen me-1"></i>Add Customer Signature
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
