<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Proforma Invoice</h1>
            <p class="text-muted mb-0">Edit proforma invoice: {{ $proformaInvoice->proforma_invoice_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to View
            </a>
            <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>PI List
            </a>
        </div>
    </div>

    <div x-data="proformaInvoiceForm({{ $proformaInvoice->contract_id }}, @js([
        'proformaInvoice' => [
            'id' => $proformaInvoice->id,
            'seller_id' => $proformaInvoice->seller_id,
            'type_of_sale' => $proformaInvoice->type_of_sale,
            'currency' => $proformaInvoice->currency,
            'usd_rate' => $proformaInvoice->usd_rate,
            'commission' => $proformaInvoice->commission,
            'buyer_company_name' => $proformaInvoice->buyer_company_name,
            'pan' => $proformaInvoice->pan,
            'gst' => $proformaInvoice->gst,
            'phone_number' => $proformaInvoice->phone_number,
            'phone_number_2' => $proformaInvoice->phone_number_2,
            'ifc_certificate_number' => $proformaInvoice->ifc_certificate_number,
            'billing_address' => $proformaInvoice->billing_address,
            'shipping_address' => $proformaInvoice->shipping_address,
            'overseas_freight' => $proformaInvoice->overseas_freight ?? 0,
            'port_expenses_clearing' => $proformaInvoice->port_expenses_clearing ?? 0,
            'gst_percentage' => ($proformaInvoice->gst_percentage !== null && $proformaInvoice->gst_percentage > 0) ? $proformaInvoice->gst_percentage : null,
            'gst_amount' => $proformaInvoice->gst_amount ?? 0,
            'notes' => $proformaInvoice->notes,
        ],
        'machines' => $proformaInvoice->proformaInvoiceMachines->map(function($piMachine) use ($proformaInvoice) {
            return [
                'tempId' => 'temp_' . $piMachine->id,
                'contract_machine_id' => (string)$piMachine->contract_machine_id,
                'machine_category_id' => (string)$piMachine->machine_category_id,
                'quantity' => $piMachine->quantity,
                'amount' => $piMachine->amount,
                'amc_price' => $piMachine->amc_price ?? 0,
                'pi_price_plus_amc' => $piMachine->pi_price_plus_amc ?? 0,
                'total_pi_price' => $piMachine->total_pi_price ?? 0,
                'description' => $piMachine->description ?? '',
                'gst_percentage' => ($proformaInvoice->gst_percentage !== null && $proformaInvoice->gst_percentage > 0) ? $proformaInvoice->gst_percentage : null,
                'overseas_freight' => $proformaInvoice->overseas_freight ?? 0,
                'port_expenses_clearing' => $proformaInvoice->port_expenses_clearing ?? 0,
                'brand_id' => $piMachine->brand_id ? (string)$piMachine->brand_id : '',
                'machine_model_id' => $piMachine->machine_model_id ? (string)$piMachine->machine_model_id : '',
                        'machine_size_id' => '',
                'feeder_id' => $piMachine->feeder_id ? (string)$piMachine->feeder_id : '',
                'machine_hook_id' => $piMachine->machine_hook_id ? (string)$piMachine->machine_hook_id : '',
                'machine_e_read_id' => $piMachine->machine_e_read_id ? (string)$piMachine->machine_e_read_id : '',
                'color_id' => $piMachine->color_id ? (string)$piMachine->color_id : '',
                'machine_nozzle_id' => $piMachine->machine_nozzle_id ? (string)$piMachine->machine_nozzle_id : '',
                'machine_dropin_id' => $piMachine->machine_dropin_id ? (string)$piMachine->machine_dropin_id : '',
                'machine_beam_id' => $piMachine->machine_beam_id ? (string)$piMachine->machine_beam_id : '',
                'machine_cloth_roller_id' => $piMachine->machine_cloth_roller_id ? (string)$piMachine->machine_cloth_roller_id : '',
                'machine_software_id' => $piMachine->machine_software_id ? (string)$piMachine->machine_software_id : '',
                'hsn_code_id' => $piMachine->hsn_code_id ? (string)$piMachine->hsn_code_id : '',
                'wir_id' => $piMachine->wir_id ? (string)$piMachine->wir_id : '',
                'machine_shaft_id' => $piMachine->machine_shaft_id ? (string)$piMachine->machine_shaft_id : '',
                'machine_lever_id' => $piMachine->machine_lever_id ? (string)$piMachine->machine_lever_id : '',
                'machine_chain_id' => $piMachine->machine_chain_id ? (string)$piMachine->machine_chain_id : '',
                'machine_heald_wire_id' => $piMachine->machine_heald_wire_id ? (string)$piMachine->machine_heald_wire_id : '',
                'delivery_term_id' => $piMachine->delivery_term_id ? (string)$piMachine->delivery_term_id : '',
                // Additional properties needed for the form
                'availableMachines' => [],
                'categoryItems' => null,
                'machineModels' => [],
                'contractAmount' => 0,
                'contractQuantityPerCategory' => 0,
                'maxQuantity' => 0,
            ];
        })->toArray(),
    ]))" x-init="init()">
        <!-- Proforma Invoice Form -->
        <div>
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Proforma Invoice Details</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('proforma-invoices.update', $proformaInvoice) }}" method="POST" id="proformaInvoiceForm" @submit="submitForm($event)">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="contract_id" value="{{ $proformaInvoice->contract_id }}">

                        <!-- Contract Info Display -->
                        <div x-show="contractData" class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Contract:</strong> <span x-text="contractData?.contract?.contract_number"></span><br>
                                    <strong>Customer:</strong> <span x-text="contractData?.contract?.buyer_name"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Sales Manager:</strong> <span x-text="contractData?.contract?.creator?.name"></span><br>
                                    <strong>Total Contract Amount:</strong> $<span x-text="contractData?.contract?.total_amount ? parseFloat(contractData.contract.total_amount).toFixed(2) : '0.00'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Buyer Details -->
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #1f2937;">Buyer Details</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Buyer Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="buyer_company_name" required 
                                           x-model="buyerCompanyName"
                                           class="form-control" placeholder="Enter Buyer Company Name" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('buyer_company_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">PAN No</label>
                                    <input type="text" name="pan" 
                                           x-model="pan"
                                           class="form-control" placeholder="Enter PAN No" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('pan')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">GST No</label>
                                    <input type="text" name="gst" 
                                           x-model="gst"
                                           class="form-control" placeholder="Enter GST No" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('gst')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" required 
                                           x-model="phoneNumber"
                                           class="form-control" placeholder="Enter Phone Number" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Phone Number 2</label>
                                    <input type="text" name="phone_number_2" 
                                           x-model="phoneNumber2"
                                           class="form-control" placeholder="Enter Phone Number 2" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number_2')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">IFC Certificate Number</label>
                                    <input type="text" name="ifc_certificate_number" 
                                           x-model="ifcCertificateNumber"
                                           class="form-control" placeholder="Enter IFC Certificate Number" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('ifc_certificate_number')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Billing and Shipping Address -->
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #1f2937;">Address Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Billing Address</label>
                                    <textarea name="billing_address" rows="3" 
                                              x-model="billingAddress"
                                              @input="if(copyBillingToShipping) { shippingAddress = billingAddress; }"
                                              class="form-control" placeholder="Enter Billing Address" 
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                    @error('billing_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-medium mb-0" style="color: #374151;">Shipping Address</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="copyBillingAddress" 
                                                   x-model="copyBillingToShipping"
                                                   @change="copyBillingAddressToShipping()"
                                                   style="cursor: pointer;">
                                            <label class="form-check-label" for="copyBillingAddress" style="cursor: pointer; color: #6b7280; font-size: 0.875rem;">
                                                <i class="fas fa-copy me-1"></i>Copy from Billing
                                            </label>
                                        </div>
                                    </div>
                                    <textarea name="shipping_address" rows="3" 
                                              x-model="shippingAddress"
                                              class="form-control" placeholder="Enter Shipping Address" 
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                    @error('shipping_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- PI Header -->
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #1f2937;">PI Header</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Seller <span class="text-danger">*</span></label>
                                    <select name="seller_id" required x-model="sellerId" @change="updatePINumber()"
                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Seller</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}" data-pi-short-name="{{ $seller->pi_short_name }}">
                                                {{ $seller->seller_name }} ({{ $seller->pi_short_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('seller_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">PI Number</label>
                                    <div class="form-control bg-light fw-medium" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <span>{{ $proformaInvoice->proforma_invoice_number }}</span>
                                    </div>
                                    <small class="text-muted">PI Number cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Type of Sale and Related Fields -->
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-3" style="color: #1f2937;">Sale Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Type Of Sale <span class="text-danger">*</span></label>
                                    <select name="type_of_sale" required x-model="typeOfSale" @change="updateCurrencyAndFields()" 
                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="import" selected>Import</option>
                                        <option value="local">Local</option>
                                        <option value="high_seas">High Seas</option>
                                    </select>
                                    @error('type_of_sale')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Currency <span class="text-danger">*</span></label>
                                    <input type="text" name="currency" required x-model="currency" readonly
                                           class="form-control" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('currency')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4" x-show="typeOfSale === 'local' || typeOfSale === 'high_seas'">
                                    <label class="form-label fw-medium" style="color: #374151;">
                                        Today Rate of USD 
                                        <span class="text-danger" x-show="typeOfSale === 'high_seas'">*</span>
                                        <span x-show="typeOfSale === 'local'" class="text-muted small">(Optional - for reference only)</span>
                                    </label>
                                    <input type="number" name="usd_rate" step="0.01" min="0" 
                                           x-model="usdRate" @input="calculateTotal()"
                                           :required="typeOfSale === 'high_seas'"
                                           class="form-control" placeholder="Enter USD Rate" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('usd_rate')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4" x-show="typeOfSale === 'high_seas'" x-cloak>
                                    <label class="form-label fw-medium" style="color: #374151;">High Seas Commission (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="commission" step="0.01" min="0" max="100"
                                           x-model="commission" @input="calculateTotal(); updateAllMachineCalculations()"
                                           :required="typeOfSale === 'high_seas'"
                                           class="form-control" placeholder="Enter Commission %" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('commission')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Machine Details Section -->
                        <div class="mb-4" x-show="contractData && contractData.machinesByCategory && contractData.machinesByCategory.length > 0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-semibold mb-0" style="color: #1f2937;">Machine Details</h5>
                                <button type="button" @click="addMachine()" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Machine
                                </button>
                            </div>

                            <!-- Added Machines List -->
                            <div x-show="addedMachines.length > 0">
                                <template x-for="(machine, index) in addedMachines" :key="machine.tempId">
                                    <div class="card shadow-sm border-0 mb-3" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-medium" style="color: #374151;">
                                                    Machine <span x-text="index + 1"></span>
                                                    <span x-show="machine.machine_category_id" class="text-muted ms-2">
                                                        (<span x-text="getCategoryName(machine.machine_category_id)"></span>)
                                                    </span>
                                                </h6>
                                                <button type="button" @click="removeMachine(machine.tempId)" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <div class="row g-3">
                                                <!-- Machine Category Selection -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium" style="color: #374151;">Machine Category <span class="text-danger">*</span></label>
                                                    <!-- Show select dropdown for new machines (no category selected yet) -->
                                                    <select 
                                                        x-show="!machine.machine_category_id || machine.machine_category_id === ''"
                                                        x-cloak
                                                        :name="`machines[${index}][machine_category_id]`"
                                                        x-model="machine.machine_category_id"
                                                        @change="loadMachinesForCategory(machine.tempId, $event.target.value)"
                                                        :required="!machine.machine_category_id || machine.machine_category_id === ''"
                                                        class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <option value="">Select Category</option>
                                                        <template x-for="category in contractData?.machinesByCategory || []" :key="category.category_id">
                                                            <option :value="category.category_id" x-text="category.category_name"></option>
                                                        </template>
                                                    </select>
                                                    <!-- Show read-only input for existing machines (category already selected) -->
                                                    <div x-show="machine.machine_category_id && machine.machine_category_id !== ''" x-cloak>
                                                        <input type="text" 
                                                               :value="getCategoryName(machine.machine_category_id)"
                                                               readonly
                                                               class="form-control bg-light" 
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <input type="hidden" 
                                                               :name="`machines[${index}][machine_category_id]`"
                                                               x-model="machine.machine_category_id">
                                                    </div>
                                                </div>

                                                <!-- Hidden field for contract machine ID (auto-selected) -->
                                                <input type="hidden" 
                                                       :name="`machines[${index}][contract_machine_id]`"
                                                       x-model="machine.contract_machine_id">

                                                <!-- Quantity -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" 
                                                           :name="`machines[${index}][quantity]`"
                                                           x-model="machine.quantity"
                                                           @input="updateMachineQuantity(machine.tempId, machine.amount, $event.target.value); calculateMachineFinalAmount(machine.tempId)"
                                                           :max="machine.maxQuantity"
                                                           min="0"
                                                           required
                                                           class="form-control" 
                                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <small class="text-muted" x-show="machine.contractQuantityPerCategory">
                                                        Contract Quantity: <span x-text="machine.contractQuantityPerCategory"></span> | 
                                                        Available: <span x-text="machine.maxQuantity" class="fw-medium"></span>
                                                        <span x-show="(usedQuantitiesByCategory[machine.machine_category_id] || 0) > 0 || getTotalQuantityUsedPerCategory(machine.machine_category_id, machine.tempId) > 0">
                                                            (Used in existing PIs: <span x-text="usedQuantitiesByCategory[machine.machine_category_id] || 0"></span>
                                                            <span x-show="getTotalQuantityUsedPerCategory(machine.machine_category_id, machine.tempId) > 0">
                                                                + Used in this form: <span x-text="getTotalQuantityUsedPerCategory(machine.machine_category_id, machine.tempId)"></span>
                                                            </span>)
                                                        </span>
                                                    </small>
                                                </div>

                                                <!-- PI Per Machine Amount -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">PI Per Machine Amount <span class="text-danger">*</span></label>
                                                    <input type="hidden"
                                                           :name="`machines[${index}][amount]`"
                                                           x-model="machine.amount">
                                                    <div class="input-group">
                                                        <span class="input-group-text" x-text="currencySymbol"></span>
                                                        <input type="number" 
                                                               :value="getMachineEditableAmount(machine.tempId)"
                                                               @input="updateMachineAmountFromDisplay(machine.tempId, $event.target.value); calculateMachineFinalAmount(machine.tempId)"
                                                               step="0.01"
                                                               min="0"
                                                               class="form-control" 
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                    <small class="text-muted">Can be modified</small>
                                                </div>

                                                <!-- Contract Machine Amount (read-only) -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id && machine.contractAmount > 0">
                                                    <label class="form-label fw-medium" style="color: #374151;">Contract Machine Amount</label>
                                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency((machine.contractAmount * machine.maxQuantity)).toFixed(2)"></span>
                                                    </div>
                                                    <small class="text-muted">Total: <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(machine.contractAmount).toFixed(2)"></span> × <span x-text="machine.maxQuantity"></span> Machines</small>
                                                </div>

                                                <!-- AMC Price -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">AMC Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" x-text="currencySymbol"></span>
                                                        <input type="number" 
                                                               :name="`machines[${index}][amc_price]`"
                                                               x-model="machine.amc_price"
                                                               @input="calculateMachineTotals(machine.tempId); calculateMachineFinalAmount(machine.tempId)"
                                                               step="0.01"
                                                               min="0"
                                                               class="form-control" 
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </div>

                                                <!-- PI Machine Amount (calculated: PI Per Machine Amount × Quantity) -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id && machine.quantity > 0">
                                                    <label class="form-label fw-medium" style="color: #374151;">PI Machine Amount</label>
                                                    <div class="form-control bg-light fw-medium" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(((machine.amount || 0) * (machine.quantity || 0))).toFixed(2)"></span>
                                                    </div>
                                                    <small class="text-muted">(PI Per Machine Amount × Quantity)</small>
                                                </div>

                                                <!-- PI-AMC Amount (calculated) -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">PI-AMC Amount</label>
                                                    <div class="form-control bg-light fw-medium" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(((parseFloat(machine.amount || 0) * parseFloat(machine.quantity || 0)) + parseFloat(machine.amc_price || 0))).toFixed(2)"></span>
                                                    </div>
                                                    <small class="text-muted">(PI Machine Amount + AMC Price)</small>
                                                </div>

                                                <!-- Commission Amount (for High Seas only) -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id && typeOfSale === 'high_seas'">
                                                    <label class="form-label fw-medium" style="color: #374151;">Commission Amount</label>
                                                    <div class="form-control bg-light fw-medium" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(getMachineCommissionAmount(machine.tempId)).toFixed(2)"></span>
                                                    </div>
                                                    <small class="text-muted">(High Seas Commission (%) × PI-AMC Amount)</small>
                                                </div>

                                                <!-- Overseas Freight Approx -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">Overseas Freight Approx</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" x-text="currencySymbol"></span>
                                                        <input type="number" 
                                                               :name="`machines[${index}][overseas_freight]`"
                                                               x-model="machine.overseas_freight"
                                                               @input="calculateMachineFinalAmount(machine.tempId)"
                                                               step="0.01"
                                                               min="0"
                                                               class="form-control" 
                                                               placeholder="Enter Overseas Freight Approx"
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </div>

                                                <!-- Port Exp + Clearing Exp Approx -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">Port Exp + Clearing Exp Approx.</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" x-text="currencySymbol"></span>
                                                        <input type="number" 
                                                               :name="`machines[${index}][port_expenses_clearing]`"
                                                               x-model="machine.port_expenses_clearing"
                                                               @input="calculateMachineFinalAmount(machine.tempId)"
                                                               step="0.01"
                                                               min="0"
                                                               class="form-control" 
                                                               placeholder="Enter Port Exp + Clearing Exp Approx"
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </div>

                                                <!-- GST Per -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-medium" style="color: #374151;">GST Per</label>
                                                    <div class="input-group">
                                                        <input type="number" 
                                                               :name="`machines[${index}][gst_percentage]`"
                                                               x-model.number="machine.gst_percentage"
                                                               @input="calculateMachineFinalAmount(machine.tempId)"
                                                               step="0.01"
                                                               min="0"
                                                               max="100"
                                                               class="form-control" 
                                                               :placeholder="machine.gst_percentage !== undefined && machine.gst_percentage !== null ? machine.gst_percentage : (gstPercentage || 'Enter GST %')"
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted" x-show="(machine.gst_percentage === undefined || machine.gst_percentage === null || machine.gst_percentage === '') && gstPercentage">
                                                        Using PI-level GST: <span x-text="gstPercentage"></span>%
                                                    </small>
                                                </div>

                                                <!-- GST Amount (calculated) -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id && machine.quantity > 0 && ((machine.gst_percentage !== undefined && machine.gst_percentage !== null && parseFloat(machine.gst_percentage) > 0) || (gstPercentage !== undefined && gstPercentage !== null && parseFloat(gstPercentage) > 0))">
                                                    <label class="form-label fw-medium" style="color: #374151;">GST Amount</label>
                                                    <div class="form-control bg-light fw-medium" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(getMachineGSTAmount(machine.tempId)).toFixed(2)"></span>
                                                        <small class="text-muted d-block mt-1" style="font-weight: normal;">
                                                            (PI-AMC Amount × GST Per)
                                                        </small>
                                                    </div>
                                                </div>

                                                <!-- Final Amount -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id && machine.quantity > 0">
                                                    <label class="form-label fw-medium" style="color: #374151;">Final Amount</label>
                                                    <div class="form-control bg-primary text-white fw-semibold" style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: var(--primary-color) !important;">
                                                        <span x-text="currencySymbol"></span><span x-text="toSaleCurrency(getMachineFinalAmount(machine.tempId)).toFixed(2)"></span>
                                                        <small class="d-block mt-1" style="font-weight: normal; opacity: 0.9;">
                                                            (Total + Freight + Port Exp + GST)
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Machine Specifications (shown when machine is selected and category items are loaded) -->
                                            <div class="row g-3 mt-3" x-show="machine.contract_machine_id && machine.categoryItems">
                                                <div class="col-12">
                                                    <hr>
                                                    <h6 class="fw-medium mb-3" style="color: #374151;">Machine Specifications</h6>
                                                </div>

                                                <!-- Brand -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.brands && machine.categoryItems.brands.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Brand</label>
                                                        <select 
                                                            :name="`machines[${index}][brand_id]`"
                                                            x-model="machine.brand_id"
                                                            @change="async () => { await loadMachineModelsForPI(machine.tempId, machine.brand_id); machine.machine_model_id = ''; }"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Brand</option>
                                                            <template x-for="brand in machine.categoryItems.brands" :key="brand.id">
                                                                <option :value="String(brand.id)" x-text="brand.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Model -->
                                                <template x-if="machine.brand_id && machine.machineModels && machine.machineModels.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Model</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_model_id]`"
                                                            x-model="machine.machine_model_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Model</option>
                                                            <template x-for="model in machine.machineModels" :key="model.id">
                                                                <option :value="String(model.id)" x-text="model.model_no"></option>
                                                            </template>
                                                        </select>
                                                        
                                                    </div>
                                                </template>

                                                <!-- Machine Size -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_sizes && machine.categoryItems.machine_sizes.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Size</label>
                                                        <select
                                                            :name="`machines[${index}][machine_size_id]`"
                                                            x-model="machine.machine_size_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Machine Size</option>
                                                            <template x-for="size in machine.categoryItems.machine_sizes" :key="size.id">
                                                                <option :value="String(size.id)" x-text="size.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Feeder -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.feeders && machine.categoryItems.feeders.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Feeder</label>
                                                        <select 
                                                            :name="`machines[${index}][feeder_id]`"
                                                            x-model="machine.feeder_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Feeder</option>
                                                            <template x-for="feeder in machine.categoryItems.feeders" :key="feeder.id">
                                                                <option :value="feeder.id" x-text="feeder.feeder"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Hook -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_hooks && machine.categoryItems.machine_hooks.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Hook</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_hook_id]`"
                                                            x-model="machine.machine_hook_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Hook</option>
                                                            <template x-for="hook in machine.categoryItems.machine_hooks" :key="hook.id">
                                                                <option :value="hook.id" x-text="hook.hook"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine E-Read -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_e_reads && machine.categoryItems.machine_e_reads.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine E-Read</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_e_read_id]`"
                                                            x-model="machine.machine_e_read_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select E-Read</option>
                                                            <template x-for="eread in machine.categoryItems.machine_e_reads" :key="eread.id">
                                                                <option :value="eread.id" x-text="eread.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Color -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.colors && machine.categoryItems.colors.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Color</label>
                                                        <select 
                                                            :name="`machines[${index}][color_id]`"
                                                            x-model="machine.color_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Color</option>
                                                            <template x-for="color in machine.categoryItems.colors" :key="color.id">
                                                                <option :value="color.id" x-text="color.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Nozzle -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_nozzles && machine.categoryItems.machine_nozzles.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Nozzle</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_nozzle_id]`"
                                                            x-model="machine.machine_nozzle_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Nozzle</option>
                                                            <template x-for="nozzle in machine.categoryItems.machine_nozzles" :key="nozzle.id">
                                                                <option :value="nozzle.id" x-text="nozzle.nozzle"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Dropin -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_dropins && machine.categoryItems.machine_dropins.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Dropin</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_dropin_id]`"
                                                            x-model="machine.machine_dropin_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Dropin</option>
                                                            <template x-for="dropin in machine.categoryItems.machine_dropins" :key="dropin.id">
                                                                <option :value="dropin.id" x-text="dropin.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Beam -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_beams && machine.categoryItems.machine_beams.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Beam</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_beam_id]`"
                                                            x-model="machine.machine_beam_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Beam</option>
                                                            <template x-for="beam in machine.categoryItems.machine_beams" :key="beam.id">
                                                                <option :value="beam.id" x-text="beam.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Cloth Roller -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_cloth_rollers && machine.categoryItems.machine_cloth_rollers.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Cloth Roller</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_cloth_roller_id]`"
                                                            x-model="machine.machine_cloth_roller_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Cloth Roller</option>
                                                            <template x-for="roller in machine.categoryItems.machine_cloth_rollers" :key="roller.id">
                                                                <option :value="roller.id" x-text="roller.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Software -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_softwares && machine.categoryItems.machine_softwares.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Software</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_software_id]`"
                                                            x-model="machine.machine_software_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Software</option>
                                                            <template x-for="software in machine.categoryItems.machine_softwares" :key="software.id">
                                                                <option :value="software.id" x-text="software.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- HSN Code -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.hsn_codes && machine.categoryItems.hsn_codes.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">HSN Code</label>
                                                        <select 
                                                            :name="`machines[${index}][hsn_code_id]`"
                                                            x-model="machine.hsn_code_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select HSN Code</option>
                                                            <template x-for="hsn in machine.categoryItems.hsn_codes" :key="hsn.id">
                                                                <option :value="hsn.id" x-text="hsn.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- WIR -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.wirs && machine.categoryItems.wirs.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">WIR</label>
                                                        <select 
                                                            :name="`machines[${index}][wir_id]`"
                                                            x-model="machine.wir_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select WIR</option>
                                                            <template x-for="wir in machine.categoryItems.wirs" :key="wir.id">
                                                                <option :value="wir.id" x-text="wir.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Shaft -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_shafts && machine.categoryItems.machine_shafts.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Shaft</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_shaft_id]`"
                                                            x-model="machine.machine_shaft_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Shaft</option>
                                                            <template x-for="shaft in machine.categoryItems.machine_shafts" :key="shaft.id">
                                                                <option :value="shaft.id" x-text="shaft.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Lever -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_levers && machine.categoryItems.machine_levers.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Lever</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_lever_id]`"
                                                            x-model="machine.machine_lever_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Lever</option>
                                                            <template x-for="lever in machine.categoryItems.machine_levers" :key="lever.id">
                                                                <option :value="lever.id" x-text="lever.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Chain -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_chains && machine.categoryItems.machine_chains.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Chain</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_chain_id]`"
                                                            x-model="machine.machine_chain_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Chain</option>
                                                            <template x-for="chain in machine.categoryItems.machine_chains" :key="chain.id">
                                                                <option :value="chain.id" x-text="chain.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Heald Wire -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_heald_wires && machine.categoryItems.machine_heald_wires.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Heald Wire</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_heald_wire_id]`"
                                                            x-model="machine.machine_heald_wire_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Heald Wire</option>
                                                            <template x-for="wire in machine.categoryItems.machine_heald_wires" :key="wire.id">
                                                                <option :value="wire.id" x-text="wire.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Delivery Term -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.delivery_terms && machine.categoryItems.delivery_terms.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Delivery Term</label>
                                                        <select 
                                                            :name="`machines[${index}][delivery_term_id]`"
                                                            x-model="machine.delivery_term_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Delivery Term</option>
                                                            <template x-for="term in machine.categoryItems.delivery_terms" :key="term.id">
                                                                <option :value="term.id" x-text="term.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Description -->
                                                <div class="col-md-12">
                                                    <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                                    <textarea 
                                                        :name="`machines[${index}][description]`"
                                                        x-model="machine.description"
                                                        rows="2"
                                                        class="form-control" 
                                                        placeholder="Additional description"
                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-medium" style="color: #374151;">Notes</label>
                            <textarea name="notes" x-model="notes" rows="3" class="form-control" 
                                      placeholder="Additional notes for proforma invoice" 
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                        </div>

                        <!-- Total Amount Display -->
                        <div class="alert alert-primary">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Total Proforma Invoice Amount:</span>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold fs-4"><span x-text="currencySymbol"></span><span x-text="displayTotalAmount.toFixed(2)"></span></span>
                                        <!-- Show USD equivalent for Local (₹ value / USD Rate) -->
                                        <span x-show="typeOfSale === 'local' && usdRate" class="fw-semibold fs-5 text-success" x-cloak>
                                            ($<span x-text="(displayTotalAmount / parseFloat(usdRate || 0)).toFixed(2)"></span>)
                                        </span>
                                    </div>
                                    <!-- Show INR equivalent for High Seas (Final Amount * USD Rate) -->
                                    <span x-show="typeOfSale === 'high_seas' && usdRate" class="fw-semibold fs-5 text-success" x-cloak>
                                        (₹<span x-text="(displayTotalAmount * parseFloat(usdRate || 0)).toFixed(2)"></span>)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" @click="cancelSelection()" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="displayTotalAmount <= 0 || !typeOfSale || !sellerId || addedMachines.length === 0">
                                <i class="fas fa-save me-2"></i>Update Proforma Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function proformaInvoiceForm(initialContractId = null, initialPIData = null) {
            return {
                selectedContractId: initialContractId,
                contractData: null,
                usedQuantitiesByCategory: {}, // Used quantities from existing PIs per category
                selectedMachines: {},
                selectedCategories: [],
                addedMachines: (initialPIData?.machines || []).map(machine => {
                    // Ensure GST percentage is set from PI-level value if not present in machine data
                    // Only set if value exists and is > 0, otherwise leave as null/undefined
                    if (machine.gst_percentage === undefined || machine.gst_percentage === null || machine.gst_percentage === '') {
                        const piGst = initialPIData?.proformaInvoice?.gst_percentage;
                        if (piGst !== undefined && piGst !== null && piGst !== '' && parseFloat(piGst) > 0) {
                            machine.gst_percentage = parseFloat(piGst);
                        } else {
                            machine.gst_percentage = null; // Don't set 0, leave as null
                        }
                    } else {
                        const parsed = parseFloat(machine.gst_percentage);
                        machine.gst_percentage = (parsed > 0) ? parsed : null; // Only set if > 0
                    }
                    // Ensure overseas_freight is set from PI-level value if not present
                    if (machine.overseas_freight === undefined || machine.overseas_freight === null || machine.overseas_freight === '') {
                        const piFreight = initialPIData?.proformaInvoice?.overseas_freight;
                        machine.overseas_freight = (piFreight !== undefined && piFreight !== null && piFreight !== '') ? parseFloat(piFreight) : 0;
                    } else {
                        machine.overseas_freight = parseFloat(machine.overseas_freight) || 0;
                    }
                    // Ensure port_expenses_clearing is set from PI-level value if not present
                    if (machine.port_expenses_clearing === undefined || machine.port_expenses_clearing === null || machine.port_expenses_clearing === '') {
                        const piPort = initialPIData?.proformaInvoice?.port_expenses_clearing;
                        machine.port_expenses_clearing = (piPort !== undefined && piPort !== null && piPort !== '') ? parseFloat(piPort) : 0;
                    } else {
                        machine.port_expenses_clearing = parseFloat(machine.port_expenses_clearing) || 0;
                    }
                    return machine;
                }),
                tempMachineIdCounter: (initialPIData?.machines?.length || 0) + 1,
                totalAmount: 0,
                sellerId: initialPIData?.proformaInvoice?.seller_id || null,
                piNumber: '{{ $proformaInvoice->proforma_invoice_number }}',
                typeOfSale: initialPIData?.proformaInvoice?.type_of_sale || 'import',
                currency: initialPIData?.proformaInvoice?.currency || 'USD',
                currencySymbol: (initialPIData?.proformaInvoice?.currency === 'INR') ? '₹' : '$',
                usdRate: initialPIData?.proformaInvoice?.usd_rate || null,
                commission: initialPIData?.proformaInvoice?.commission || null,
                displayTotalAmount: 0,
                buyerCompanyName: initialPIData?.proformaInvoice?.buyer_company_name || '',
                pan: initialPIData?.proformaInvoice?.pan || '',
                gst: initialPIData?.proformaInvoice?.gst || '',
                phoneNumber: initialPIData?.proformaInvoice?.phone_number || '',
                phoneNumber2: initialPIData?.proformaInvoice?.phone_number_2 || '',
                ifcCertificateNumber: initialPIData?.proformaInvoice?.ifc_certificate_number || '',
                billingAddress: initialPIData?.proformaInvoice?.billing_address || '',
                shippingAddress: initialPIData?.proformaInvoice?.shipping_address || '',
                copyBillingToShipping: false,
                amcPrice: 0,
                overseasFreight: initialPIData?.proformaInvoice?.overseas_freight || 0,
                portExpensesClearing: initialPIData?.proformaInvoice?.port_expenses_clearing || 0,
                gstPercentage: (initialPIData?.proformaInvoice?.gst_percentage !== undefined && initialPIData?.proformaInvoice?.gst_percentage !== null && parseFloat(initialPIData?.proformaInvoice?.gst_percentage) > 0) ? parseFloat(initialPIData?.proformaInvoice?.gst_percentage) : null,
                gstAmount: initialPIData?.proformaInvoice?.gst_amount || 0,
                finalAmountWithGST: 0,
                notes: initialPIData?.proformaInvoice?.notes || '',

                async init() {
                    // Initialize default values
                    this.updateCurrencyAndFields();
                    
                    // Load contract data first
                    if (this.selectedContractId) {
                        await this.selectContract(this.selectedContractId, true);
                        
                        // Pre-populate machines if editing (after contract is loaded)
                        if (initialPIData?.machines && initialPIData.machines.length > 0 && this.contractData) {
                            // Set selected categories based on machines
                            const categoryIds = [...new Set(initialPIData.machines.map(m => m.machine_category_id))];
                            this.selectedCategories = categoryIds;
                            
                            // Load details for each pre-populated machine
                            for (const machineData of initialPIData.machines) {
                                const machine = this.addedMachines.find(m => m.tempId === machineData.tempId);
                                if (machine && machine.contract_machine_id) {
                                    // Save the original values from machineData (source of truth) before loadMachineDetails overwrites them
                                    const savedQuantity = machineData.quantity || 0;
                                    const savedAmount = machineData.amount || 0;
                                    const savedAmcPrice = machineData.amc_price || 0;
                                    const savedDescription = machineData.description || '';
                                    // Get GST percentage - check if machineData has it, otherwise use PI-level gstPercentage
                                    // Only set if value is > 0, otherwise leave as null
                                    let savedGstPercentage = null;
                                    if (machineData.gst_percentage !== undefined && machineData.gst_percentage !== null && machineData.gst_percentage !== '') {
                                        const parsed = parseFloat(machineData.gst_percentage);
                                        savedGstPercentage = (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                    }
                                    // If machine data doesn't have GST or it's 0, check PI-level
                                    if (savedGstPercentage === null && this.gstPercentage !== undefined && this.gstPercentage !== null && this.gstPercentage !== '' && parseFloat(this.gstPercentage) > 0) {
                                        savedGstPercentage = parseFloat(this.gstPercentage);
                                    }
                                    // Save brand and model IDs (important - these are strings) - get from machineData which is the source of truth
                                    const savedBrandId = (machineData.brand_id && machineData.brand_id !== '' && machineData.brand_id !== null && machineData.brand_id !== undefined && machineData.brand_id !== '0') ? String(machineData.brand_id) : '';
                                    const savedModelId = (machineData.machine_model_id && machineData.machine_model_id !== '' && machineData.machine_model_id !== null && machineData.machine_model_id !== undefined && machineData.machine_model_id !== '0') ? String(machineData.machine_model_id) : '';
                                    
                                    // STEP 1: Populate availableMachines for this category first (needed for form display)
                                    if (machine.machine_category_id) {
                                        const machines = this.getMachinesForCategory(String(machine.machine_category_id));
                                        if (machines && machines.length > 0) {
                                            machine.availableMachines = machines.map(m => ({...m}));
                                        }
                                        
                                        // STEP 2: Load category items FIRST - this is critical for all specification dropdowns to work
                                        await this.loadCategoryItemsForMachine(machine.tempId, machine.machine_category_id);
                                        await this.$nextTick();
                                        await this.$nextTick(); // Extra tick to ensure categoryItems is fully loaded
                                    }
                                    
                                    // STEP 3: Temporarily clear brand_id and model_id to prevent loadMachineDetails from interfering
                                    // We'll restore them after loadMachineDetails completes
                                    machine.brand_id = '';
                                    machine.machine_model_id = '';
                                    
                                    // STEP 4: Load machine details (will set contractAmount, maxQuantity, etc.)
                                    // NOTE: This will NOT load category items again if already loaded, but will set contract specs
                                    await this.loadMachineDetails(machine.tempId, machine.contract_machine_id);
                                    await this.$nextTick();
                                    
                                    // STEP 5: Restore all saved PI values (overwrite contract values from loadMachineDetails)
                                    machine.quantity = savedQuantity;
                                    // Preserve the saved PI amount (user may have changed it from contract amount)
                                    machine.amount = savedAmount > 0 ? savedAmount : (machine.contractAmount || 0);
                                    machine.amc_price = savedAmcPrice;
                                    machine.description = savedDescription;
                                    // Set GST percentage - only set if > 0, otherwise leave as null
                                    if (savedGstPercentage !== null && savedGstPercentage !== undefined && !isNaN(parseFloat(savedGstPercentage)) && parseFloat(savedGstPercentage) > 0) {
                                        machine.gst_percentage = parseFloat(savedGstPercentage);
                                    } else {
                                        // Check PI-level GST
                                        if (this.gstPercentage !== undefined && this.gstPercentage !== null && this.gstPercentage !== '' && parseFloat(this.gstPercentage) > 0) {
                                            machine.gst_percentage = parseFloat(this.gstPercentage);
                                        } else {
                                            machine.gst_percentage = null; // Leave empty, don't set 0
                                        }
                                    }
                                    
                                    // Use PI-level values for overseas_freight and port_expenses_clearing if machine-level values are 0 or not set
                                    machine.overseas_freight = (machineData.overseas_freight && parseFloat(machineData.overseas_freight) > 0) ? machineData.overseas_freight : (this.overseasFreight || 0);
                                    machine.port_expenses_clearing = (machineData.port_expenses_clearing && parseFloat(machineData.port_expenses_clearing) > 0) ? machineData.port_expenses_clearing : (this.portExpensesClearing || 0);
                                    
                                    // Force recalculation after setting all values
                                    await this.$nextTick();
                                    this.calculateMachineFinalAmount(machine.tempId);
                                    
                                    // STEP 6: Restore all other specification IDs FIRST (before brand/model - brand/model need to be restored last)
                                    // Restore all other specification IDs - ensure values match option value formats
                                    // Check each field and set only if it has a value
                                    const specFields = {
                                        'feeder_id': 'feeder_id',
                                        'machine_hook_id': 'machine_hook_id',
                                        'machine_e_read_id': 'machine_e_read_id',
                                        'color_id': 'color_id',
                                        'machine_nozzle_id': 'machine_nozzle_id',
                                        'machine_dropin_id': 'machine_dropin_id',
                                        'machine_beam_id': 'machine_beam_id',
                                        'machine_cloth_roller_id': 'machine_cloth_roller_id',
                                        'machine_software_id': 'machine_software_id',
                                        'hsn_code_id': 'hsn_code_id',
                                        'wir_id': 'wir_id',
                                        'machine_shaft_id': 'machine_shaft_id',
                                        'machine_lever_id': 'machine_lever_id',
                                        'machine_chain_id': 'machine_chain_id',
                                        'machine_heald_wire_id': 'machine_heald_wire_id',
                                        'delivery_term_id': 'delivery_term_id'
                                    };
                                    
                                    // Set all specification values from saved data
                                    // Match the option value format: most use raw ID (number), brand/model use String(id)
                                    for (const [key, value] of Object.entries(specFields)) {
                                        if (machineData[value] !== null && machineData[value] !== undefined && machineData[value] !== '') {
                                            // For most fields, keep as number (matches :value="feeder.id" format)
                                            // Alpine.js x-model handles type coercion, but matching types is safer
                                            const numValue = parseInt(machineData[value]);
                                            machine[key] = isNaN(numValue) ? machineData[value] : numValue;
                                        }
                                    }
                                    
                                    // Force Alpine.js to update after all specs are set
                                    await this.$nextTick();
                                    await this.$nextTick(); // Extra tick to ensure DOM reactivity
                                    
                                    // STEP 7: Restore brand and model LAST (MUST be after all other specs are restored)
                                    // Ensure categoryItems is loaded before restoring brand
                                    if (!machine.categoryItems || !machine.categoryItems.brands) {
                                        await this.loadCategoryItemsForMachine(machine.tempId, machine.machine_category_id);
                                        await this.$nextTick();
                                        await this.$nextTick();
                                    }
                                    
                                    // Restore brand and model from saved data (use savedBrandId/savedModelId which are from machineData)
                                    if (savedBrandId && savedBrandId !== '' && savedBrandId !== null && savedBrandId !== undefined && savedBrandId !== '0') {
                                        // Set brand_id - must be String to match :value="String(brand.id)"
                                        machine.brand_id = String(savedBrandId);
                                        
                                        // Force Alpine.js reactivity
                                        await this.$nextTick();
                                        await this.$nextTick();
                                        
                                        // Load machine models for this brand (required for model dropdown to show options)
                                        await this.loadMachineModelsForPI(machine.tempId, savedBrandId);
                                        
                                        // Wait for models API response to complete - give it more time
                                        await new Promise(resolve => setTimeout(resolve, 500));
                                        await this.$nextTick();
                                        await this.$nextTick();
                                        await this.$nextTick();
                                        
                                        // Restore model AFTER models are loaded
                                        if (savedModelId && savedModelId !== '' && savedModelId !== null && savedModelId !== undefined && savedModelId !== '0') {
                                            // Set model_id - must be String to match :value="String(model.id)"
                                            machine.machine_model_id = String(savedModelId);
                                            
                                            // Force multiple reactivity updates to ensure DOM updates
                                            await this.$nextTick();
                                            await this.$nextTick();
                                            await this.$nextTick();
                                            
                                            // Additional wait to ensure DOM is fully updated
                                            await new Promise(resolve => setTimeout(resolve, 200));
                                        }
                                    }
                                    
                                    // Recalculate totals for this machine
                                    this.calculateMachineTotals(machine.tempId);
                                    this.updateAvailableQuantity(machine.tempId);
                                }
                            }
                            
                            // Trigger final calculation after all machines are loaded
                            await this.$nextTick();
                            this.calculateFinalAmounts();
                        } else {
                            this.calculateFinalAmounts();
                        }
                    } else {
                        this.calculateFinalAmounts();
                    }
                },

                updateCurrencyAndFields() {
                    if (this.typeOfSale === 'import') {
                        this.currency = 'USD';
                        this.currencySymbol = '$';
                        this.usdRate = null;
                        this.commission = null;
                    } else if (this.typeOfSale === 'local') {
                        this.currency = 'INR';
                        this.currencySymbol = '₹';
                        this.commission = null;
                    } else if (this.typeOfSale === 'high_seas') {
                        this.currency = 'USD';
                        this.currencySymbol = '$';
                        // Set default commission to 2% if not already set
                        if (!this.commission || this.commission === null) {
                            this.commission = 2;
                        }
                    }
                    this.calculateTotal();
                },

                updatePINumber() {
                    if (!this.sellerId) {
                        this.piNumber = '';
                        return;
                    }
                    
                    // Get the selected seller option
                    const sellerSelect = document.querySelector('select[name="seller_id"]');
                    const selectedOption = sellerSelect.options[sellerSelect.selectedIndex];
                    const piShortName = selectedOption.getAttribute('data-pi-short-name');
                    
                    if (!piShortName) {
                        this.piNumber = '';
                        return;
                    }
                    
                    // Format: PI_SHORT_NAME + ddmmyyyy
                    const today = new Date();
                    const day = String(today.getDate()).padStart(2, '0');
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const year = today.getFullYear();
                    const dateStr = day + month + year; // ddmmyyyy format
                    
                    // Base PI number
                    const basePINumber = piShortName + dateStr;
                    this.piNumber = basePINumber;
                    
                    // Note: The server will handle duplicate checking and add _A, _B, etc. if needed
                    // This is just a preview of what the PI number will be
                },

                async selectContract(contractId, skipReset = false) {
                    this.selectedContractId = contractId;
                    
                    if (!skipReset) {
                        this.selectedMachines = {};
                        this.addedMachines = [];
                        this.totalAmount = 0;
                    }

                    try {
                        const response = await fetch(`/contracts/${contractId}/contract-details?exclude_pi_id={{ $proformaInvoice->id }}`);
                        const data = await response.json();
                        this.contractData = data;
                        
                        // Store used quantities by category from existing PIs
                        this.usedQuantitiesByCategory = data.usedQuantitiesByCategory || {};

                        // Only populate buyer details from contract if not already set (editing)
                        if (!skipReset && data.contract) {
                            if (!this.buyerCompanyName) this.buyerCompanyName = data.contract.company_name || '';
                            if (!this.pan) this.pan = data.contract.pan || '';
                            if (!this.gst) this.gst = data.contract.gst || '';
                            if (!this.phoneNumber) this.phoneNumber = data.contract.phone_number || '';
                            if (!this.phoneNumber2) this.phoneNumber2 = data.contract.phone_number_2 || '';
                            // Populate addresses from contract contact_address only if not set
                            if (!this.billingAddress) this.billingAddress = data.contract.contact_address || '';
                            if (!this.shippingAddress) this.shippingAddress = data.contract.contact_address || '';
                        }
                    } catch (error) {
                        console.error('Error loading contract details:', error);
                        alert('Failed to load contract details. Please try again.');
                    }
                },


                async addMachine() {
                    const tempId = 'temp_' + (++this.tempMachineIdCounter);
                    const newMachine = {
                        tempId: tempId,
                        machine_category_id: '', // User will select category
                        contract_machine_id: '',
                        availableMachines: [], // Will be populated by loadMachinesForCategory
                        categoryItems: null, // Will be populated by loadCategoryItemsForMachine
                        machineModels: [], // Will be populated by loadMachineModelsForPI
                        quantity: 0,
                        amount: 0,
                        amc_price: 0,
                        contractAmount: 0, // Original contract amount
                        maxQuantity: 0, // Original contract quantity for this machine
                        contractQuantityPerCategory: 0, // Original contract quantity for this category
                        overseas_freight: this.overseasFreight || 0,
                        port_expenses_clearing: this.portExpensesClearing || 0,
                        gst_percentage: (this.gstPercentage !== undefined && this.gstPercentage !== null && this.gstPercentage !== '' && parseFloat(this.gstPercentage) > 0) ? parseFloat(this.gstPercentage) : null,
                        brand_id: '',
                        machine_model_id: '',
                        machine_size_id: '',
                        feeder_id: '',
                        machine_hook_id: '',
                        machine_e_read_id: '',
                        color_id: '',
                        machine_nozzle_id: '',
                        machine_dropin_id: '',
                        machine_beam_id: '',
                        machine_cloth_roller_id: '',
                        machine_software_id: '',
                        hsn_code_id: '',
                        wir_id: '',
                        machine_shaft_id: '',
                        machine_lever_id: '',
                        machine_chain_id: '',
                        machine_heald_wire_id: '',
                        delivery_term_id: '',
                        delivery_term_text: '',
                        description: ''
                    };
                    
                    this.addedMachines.push(newMachine);
                    
                    // Force Alpine.js reactivity update
                    await this.$nextTick();
                },

                removeMachine(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    const categoryId = machine ? machine.machine_category_id : null;
                    
                    this.addedMachines = this.addedMachines.filter(m => m.tempId !== tempId);
                    
                    // Update available quantities for remaining machines in the same category
                    if (categoryId) {
                        this.addedMachines.forEach(m => {
                            if (m.machine_category_id == categoryId && m.contract_machine_id) {
                                this.updateAvailableQuantity(m.tempId);
                            }
                        });
                    }
                    
                    this.calculateTotal();
                },

                getMachinesForCategory(categoryId) {
                    if (!this.contractData || !this.contractData.machinesByCategory || !categoryId) {
                        return [];
                    }
                    // Convert both to strings for comparison to handle type mismatches
                    const categoryIdStr = String(categoryId);
                    const category = this.contractData.machinesByCategory.find(cat => {
                        const catIdStr = String(cat.category_id);
                        return catIdStr === categoryIdStr;
                    });
                    
                    if (category && category.machines && Array.isArray(category.machines) && category.machines.length > 0) {
                        return category.machines;
                    }
                    return [];
                },

                async loadMachinesForCategory(tempId, categoryId) {
                    if (!categoryId) {
                        // If no category, clear available machines
                        const machine = this.addedMachines.find(m => m.tempId === tempId);
                        if (machine) {
                            machine.availableMachines = [];
                            machine.categoryItems = null;
                            machine.contract_machine_id = '';
                            machine.machine_size_id = '';
                            machine.quantity = 0;
                            machine.maxQuantity = 0;
                            machine.contractQuantityPerCategory = 0;
                        }
                        return;
                    }
                    
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        const categoryIdStr = String(categoryId);
                        const oldCategoryId = machine.machine_category_id;
                        machine.machine_category_id = categoryIdStr;
                        
                        // If category changed, reset quantity and update available quantities
                        if (oldCategoryId && oldCategoryId !== categoryIdStr) {
                            machine.quantity = 0;
                            machine.maxQuantity = 0;
                            machine.contractQuantityPerCategory = 0;
                            // Update available quantities for old category
                            this.addedMachines.forEach(m => {
                                if (m.machine_category_id == oldCategoryId && m.tempId !== tempId && m.contract_machine_id) {
                                    this.updateAvailableQuantity(m.tempId);
                                }
                            });
                        }
                        
                        // Store available machines for this category - create new array for reactivity
                        const machines = this.getMachinesForCategory(categoryIdStr);
                        // Force reactivity by creating a completely new array reference
                        if (machines && machines.length > 0) {
                            machine.availableMachines = machines.map(m => ({...m})); // Deep copy for reactivity
                            
                            // If machine already has a contract_machine_id (edit mode), try to preserve it
                            // Otherwise, automatically select the first machine from this category
                            if (machine.contract_machine_id) {
                                // Check if the current contract_machine_id exists in the new category's machines
                                const existingMachine = machines.find(m => String(m.id) === String(machine.contract_machine_id));
                                if (!existingMachine) {
                                    // Current contract machine not in new category, select first one
                                    const firstMachine = machines[0];
                                    if (firstMachine) {
                                        machine.contract_machine_id = String(firstMachine.id);
                                        // Automatically load all details from the contract machine
                                        await this.loadMachineDetails(tempId, firstMachine.id);
                                    }
                                }
                                // If existing machine is found, keep the current contract_machine_id
                                // Don't reload details as they're already loaded
                            } else {
                                // No existing selection, automatically select the first machine
                                const firstMachine = machines[0];
                                if (firstMachine) {
                                    machine.contract_machine_id = String(firstMachine.id);
                                    // Automatically load all details from the contract machine
                                    await this.loadMachineDetails(tempId, firstMachine.id);
                                }
                            }
                        } else {
                            machine.availableMachines = [];
                            machine.contract_machine_id = '';
                        }
                    }
                },

                async loadMachineDetails(tempId, contractMachineId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !contractMachineId) {
                        console.log('loadMachineDetails: Missing machine or contractMachineId', { tempId, contractMachineId, machine });
                        return;
                    }
                    
                    // Find the contract machine details - try multiple comparison methods
                    const contractMachineIdNum = parseInt(contractMachineId);
                    const contractMachineIdStr = String(contractMachineId);
                    
                    // Get all machines from all categories
                    const allMachines = this.contractData.machinesByCategory.flatMap(cat => cat.machines);
                    
                    // Try to find the machine using multiple comparison methods
                    let contractMachine = allMachines.find(m => {
                        return m.id == contractMachineIdNum || 
                               String(m.id) === contractMachineIdStr ||
                               m.id === contractMachineIdNum ||
                               String(m.id) === String(contractMachineIdNum);
                    });
                    
                    if (!contractMachine) {
                        console.log('loadMachineDetails: Contract machine not found', {
                            contractMachineId,
                            contractMachineIdNum,
                            contractMachineIdStr,
                            allMachineIds: allMachines.map(m => ({ id: m.id, type: typeof m.id, brand: m.brand, model: m.model }))
                        });
                        return;
                    }
                    
                    // Set contract machine ID
                    machine.contract_machine_id = String(contractMachine.id);
                    machine.contractAmount = parseFloat(contractMachine.amount) || 0; // Store original contract amount
                    
                    // Only set amount from contract if machine doesn't already have a saved amount (edit mode)
                    // In edit mode, we want to preserve the user's custom PI amount
                    // Check if amount is undefined/null, not just 0, because 0 might be a valid saved value
                    if (machine.amount === undefined || machine.amount === null) {
                        machine.amount = parseFloat(contractMachine.amount) || 0; // Set editable amount (can be changed)
                    }
                    // If machine already has amc_price set (edit mode), preserve it, otherwise initialize to 0
                    if (machine.amc_price === undefined || machine.amc_price === null) {
                        machine.amc_price = 0;
                    }
                    
                    // Get contract quantity from category data (includes used quantities from existing PIs)
                    const categoryIdStr = String(machine.machine_category_id);
                    let contractQty = parseInt(contractMachine.quantity) || 0;
                    
                    // Try to get from category data (which includes used quantities info)
                    if (this.contractData && this.contractData.machinesByCategory) {
                        const category = this.contractData.machinesByCategory.find(cat => String(cat.category_id) === categoryIdStr);
                        if (category) {
                            contractQty = category.contract_quantity || contractQty;
                        }
                    }
                    
                    machine.contractQuantityPerCategory = contractQty; // Store contract quantity for this category
                    
                    // Only reset quantity if it's not already set (preserve in edit mode)
                    // Check if quantity is undefined/null, not just 0
                    if (machine.quantity === undefined || machine.quantity === null) {
                        machine.quantity = 0; // Start with 0, user will select
                    }
                    
                    // Update available quantity based on other machines in same category and existing PIs
                    this.updateAvailableQuantity(machine.tempId);
                    
                    // Only initialize these if not already set (preserve in edit mode)
                    if (machine.overseas_freight === undefined || machine.overseas_freight === null) {
                        machine.overseas_freight = 0;
                    }
                    if (machine.port_expenses_clearing === undefined || machine.port_expenses_clearing === null) {
                        machine.port_expenses_clearing = 0;
                    }
                    // Only set GST percentage if PI-level GST is > 0, otherwise leave as null
                    if ((machine.gst_percentage === undefined || machine.gst_percentage === null || machine.gst_percentage === '') && this.gstPercentage !== undefined && this.gstPercentage !== null && this.gstPercentage !== '' && parseFloat(this.gstPercentage) > 0) {
                        machine.gst_percentage = parseFloat(this.gstPercentage);
                    } else if (machine.gst_percentage === undefined || machine.gst_percentage === null || machine.gst_percentage === '') {
                        machine.gst_percentage = null; // Don't set 0, leave empty
                    }
                    
                    // Load category items for this machine's category FIRST (only if not already loaded)
                    if (machine.machine_category_id && !machine.categoryItems) {
                        await this.loadCategoryItemsForMachine(tempId, machine.machine_category_id);
                        // Wait for Alpine.js to process the category items
                        await this.$nextTick();
                    }
                    
                    // Store contract values - convert to strings to match option values
                    const brandId = (contractMachine.brand_id != null && contractMachine.brand_id !== '') ? String(contractMachine.brand_id) : '';
                    const modelId = (contractMachine.machine_model_id != null && contractMachine.machine_model_id !== '') ? String(contractMachine.machine_model_id) : '';
                    
                    // Set brand_id FIRST (but NOT model_id yet)
                    // IMPORTANT: In edit mode, DO NOT overwrite brand_id if it's already set from saved data
                    // Only set from contract if brand_id is empty/undefined/null (create mode)
                    if (machine.brand_id === undefined || machine.brand_id === null || machine.brand_id === '' || machine.brand_id === '0') {
                        machine.brand_id = brandId;
                    }
                    // Only clear model_id if not already set (preserve edit mode values)
                    if (machine.machine_model_id === undefined || machine.machine_model_id === null || machine.machine_model_id === '' || machine.machine_model_id === '0') {
                        machine.machine_model_id = '';
                    }

                    // Set machine size id (if provided by contract details endpoint)
                    if (machine.machine_size_id === undefined || machine.machine_size_id === null || machine.machine_size_id === '') {
                        machine.machine_size_id = (contractMachine.machine_size_id != null && contractMachine.machine_size_id !== '') ? parseInt(contractMachine.machine_size_id) || contractMachine.machine_size_id : '';
                    }
                    
                    // Set all other specification values from contract (only if not already set - preserve edit mode values)
                    // Use number format for most fields to match option :value format
                    if (machine.feeder_id === undefined || machine.feeder_id === null || machine.feeder_id === '') {
                        machine.feeder_id = (contractMachine.feeder_id != null && contractMachine.feeder_id !== '') ? parseInt(contractMachine.feeder_id) || contractMachine.feeder_id : '';
                    }
                    if (machine.machine_hook_id === undefined || machine.machine_hook_id === null || machine.machine_hook_id === '') {
                        machine.machine_hook_id = (contractMachine.machine_hook_id != null && contractMachine.machine_hook_id !== '') ? parseInt(contractMachine.machine_hook_id) || contractMachine.machine_hook_id : '';
                    }
                    if (machine.machine_e_read_id === undefined || machine.machine_e_read_id === null || machine.machine_e_read_id === '') {
                        machine.machine_e_read_id = (contractMachine.machine_e_read_id != null && contractMachine.machine_e_read_id !== '') ? parseInt(contractMachine.machine_e_read_id) || contractMachine.machine_e_read_id : '';
                    }
                    if (machine.color_id === undefined || machine.color_id === null || machine.color_id === '') {
                        machine.color_id = (contractMachine.color_id != null && contractMachine.color_id !== '') ? parseInt(contractMachine.color_id) || contractMachine.color_id : '';
                    }
                    if (machine.machine_nozzle_id === undefined || machine.machine_nozzle_id === null || machine.machine_nozzle_id === '') {
                        machine.machine_nozzle_id = (contractMachine.machine_nozzle_id != null && contractMachine.machine_nozzle_id !== '') ? parseInt(contractMachine.machine_nozzle_id) || contractMachine.machine_nozzle_id : '';
                    }
                    if (machine.machine_dropin_id === undefined || machine.machine_dropin_id === null || machine.machine_dropin_id === '') {
                        machine.machine_dropin_id = (contractMachine.machine_dropin_id != null && contractMachine.machine_dropin_id !== '') ? parseInt(contractMachine.machine_dropin_id) || contractMachine.machine_dropin_id : '';
                    }
                    if (machine.machine_beam_id === undefined || machine.machine_beam_id === null || machine.machine_beam_id === '') {
                        machine.machine_beam_id = (contractMachine.machine_beam_id != null && contractMachine.machine_beam_id !== '') ? parseInt(contractMachine.machine_beam_id) || contractMachine.machine_beam_id : '';
                    }
                    if (machine.machine_cloth_roller_id === undefined || machine.machine_cloth_roller_id === null || machine.machine_cloth_roller_id === '') {
                        machine.machine_cloth_roller_id = (contractMachine.machine_cloth_roller_id != null && contractMachine.machine_cloth_roller_id !== '') ? parseInt(contractMachine.machine_cloth_roller_id) || contractMachine.machine_cloth_roller_id : '';
                    }
                    if (machine.machine_software_id === undefined || machine.machine_software_id === null || machine.machine_software_id === '') {
                        machine.machine_software_id = (contractMachine.machine_software_id != null && contractMachine.machine_software_id !== '') ? parseInt(contractMachine.machine_software_id) || contractMachine.machine_software_id : '';
                    }
                    if (machine.hsn_code_id === undefined || machine.hsn_code_id === null || machine.hsn_code_id === '') {
                        machine.hsn_code_id = (contractMachine.hsn_code_id != null && contractMachine.hsn_code_id !== '') ? parseInt(contractMachine.hsn_code_id) || contractMachine.hsn_code_id : '';
                    }
                    if (machine.wir_id === undefined || machine.wir_id === null || machine.wir_id === '') {
                        machine.wir_id = (contractMachine.wir_id != null && contractMachine.wir_id !== '') ? parseInt(contractMachine.wir_id) || contractMachine.wir_id : '';
                    }
                    if (machine.machine_shaft_id === undefined || machine.machine_shaft_id === null || machine.machine_shaft_id === '') {
                        machine.machine_shaft_id = (contractMachine.machine_shaft_id != null && contractMachine.machine_shaft_id !== '') ? parseInt(contractMachine.machine_shaft_id) || contractMachine.machine_shaft_id : '';
                    }
                    if (machine.machine_lever_id === undefined || machine.machine_lever_id === null || machine.machine_lever_id === '') {
                        machine.machine_lever_id = (contractMachine.machine_lever_id != null && contractMachine.machine_lever_id !== '') ? parseInt(contractMachine.machine_lever_id) || contractMachine.machine_lever_id : '';
                    }
                    if (machine.machine_chain_id === undefined || machine.machine_chain_id === null || machine.machine_chain_id === '') {
                        machine.machine_chain_id = (contractMachine.machine_chain_id != null && contractMachine.machine_chain_id !== '') ? parseInt(contractMachine.machine_chain_id) || contractMachine.machine_chain_id : '';
                    }
                    if (machine.machine_heald_wire_id === undefined || machine.machine_heald_wire_id === null || machine.machine_heald_wire_id === '') {
                        machine.machine_heald_wire_id = (contractMachine.machine_heald_wire_id != null && contractMachine.machine_heald_wire_id !== '') ? parseInt(contractMachine.machine_heald_wire_id) || contractMachine.machine_heald_wire_id : '';
                    }
                    if (machine.delivery_term_id === undefined || machine.delivery_term_id === null || machine.delivery_term_id === '') {
                        machine.delivery_term_id = (contractMachine.delivery_term_id != null && contractMachine.delivery_term_id !== '') ? parseInt(contractMachine.delivery_term_id) || contractMachine.delivery_term_id : '';
                    }
                    machine.description = contractMachine.description || '';
                    
                    // Wait for Alpine.js to process brand_id change
                    await this.$nextTick();
                    
                    // Load machine models if brand is selected
                    // IMPORTANT: Only load models for contract brand if machine doesn't already have a saved brand
                    // In edit mode, the saved brand will be restored later, so we don't want to load models for contract brand
                    const currentBrandId = machine.brand_id && machine.brand_id !== '' && machine.brand_id !== '0' ? String(machine.brand_id) : null;
                    const brandToUseForModels = currentBrandId || brandId;
                    
                    if (brandToUseForModels) {
                        await this.loadMachineModelsForPI(tempId, brandToUseForModels);
                        // Wait for models to load and Alpine to update
                        await this.$nextTick();
                        await this.$nextTick(); // Extra tick to ensure DOM is updated
                        
                        // AFTER models are loaded, set the model_id from contract (only if machine doesn't already have a saved model)
                        // In edit mode, saved model will be restored later
                        const currentModelId = machine.machine_model_id && machine.machine_model_id !== '' && machine.machine_model_id !== '0' ? String(machine.machine_model_id) : null;
                        const modelToSet = currentModelId || modelId;
                        
                        if (modelToSet && machine.machineModels && machine.machineModels.length > 0) {
                            // Check if the model exists in the loaded models
                            const modelExists = machine.machineModels.some(m => String(m.id) === String(modelToSet));
                            if (modelExists) {
                                // Set the model_id - this should now work because models are loaded
                                machine.machine_model_id = String(modelToSet);
                                // Force another update to ensure reactivity
                                await this.$nextTick();
                                console.log('Model set from', currentModelId ? 'saved data' : 'contract', ':', modelToSet, 'Available models:', machine.machineModels.map(m => ({id: m.id, model_no: m.model_no})));
                            } else {
                                console.warn('Model not found in loaded models:', modelToSet, 'Available models:', machine.machineModels.map(m => ({id: m.id, model_no: m.model_no})));
                                // Still try to set it in case it's a display issue
                                machine.machine_model_id = String(modelToSet);
                            }
                        } else if (modelToSet) {
                            // If we have a modelId but no models loaded, still set it
                            console.warn('No models loaded but', currentModelId ? 'saved data' : 'contract', 'has model_id:', modelToSet);
                            machine.machine_model_id = String(modelToSet);
                        }
                    }
                    
                    // Force reactivity update
                    await this.$nextTick();
                    
                    // Update available quantities for all machines in same category
                    this.updateAvailableQuantity(tempId);
                    
                    this.calculateTotal();
                },

                async loadCategoryItemsForMachine(tempId, categoryId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !categoryId) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('leads/category-items') }}/${categoryId}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        const items = await response.json();
                        machine.categoryItems = items;
                    } catch (error) {
                        console.error('Error loading category items:', error);
                        machine.categoryItems = null;
                        // Show user-friendly error message
                        alert('Failed to load category items. Please check your permissions or try refreshing the page.');
                    }
                },

                async loadMachineModelsForPI(tempId, brandId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !brandId) {
                        console.log('loadMachineModelsForPI: Missing machine or brandId', { tempId, brandId, machine });
                        return;
                    }

                    try {
                        const brandIdStr = String(brandId);
                        const categoryIdStr = machine.machine_category_id ? String(machine.machine_category_id) : '';
                        const query = categoryIdStr ? `?category_id=${encodeURIComponent(categoryIdStr)}` : '';
                        console.log('Loading machine models for brand:', brandIdStr);
                        const response = await fetch(`{{ url('leads/machine-models') }}/${brandIdStr}${query}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        const models = await response.json();
                        // Ensure we have an array and models have proper structure
                        machine.machineModels = Array.isArray(models) ? models : [];
                        console.log('Loaded machine models for brand', brandIdStr, ':', machine.machineModels.length, 'models', machine.machineModels);
                        
                        // Force reactivity by creating new array reference
                        machine.machineModels = [...machine.machineModels];
                    } catch (error) {
                        console.error('Error loading machine models:', error);
                        machine.machineModels = [];
                        // Show user-friendly error message only if it's a permission error
                        if (error.message && error.message.includes('403')) {
                            alert('Failed to load machine models. Please check your permissions or try refreshing the page.');
                        }
                    }
                },

                getCategoryName(categoryId) {
                    if (!this.contractData || !this.contractData.machinesByCategory || !categoryId) {
                        return '';
                    }
                    const category = this.contractData.machinesByCategory.find(cat => String(cat.category_id) === String(categoryId));
                    return category ? category.category_name : '';
                },

                getSelectedMachineInfo(contractMachineId) {
                    if (!contractMachineId || !this.contractData || !this.contractData.machinesByCategory) {
                        return 'No machine selected';
                    }
                    
                    // Get all machines from all categories
                    const allMachines = this.contractData.machinesByCategory.flatMap(cat => cat.machines);
                    const machine = allMachines.find(m => String(m.id) === String(contractMachineId));
                    
                    if (machine) {
                        return (machine.brand || 'N/A') + ' - ' + (machine.model || 'N/A');
                    }
                    return 'Machine not found';
                },

                getTotalQuantityUsedPerCategory(categoryId, excludeTempId = null) {
                    // Get total quantity used by all machines in this category (excluding current machine)
                    return this.addedMachines
                        .filter(m => m.machine_category_id == categoryId && m.tempId !== excludeTempId && m.contract_machine_id)
                        .reduce((sum, m) => sum + (parseInt(m.quantity) || 0), 0);
                },

                getAvailableQuantityForCategory(categoryId, excludeTempId = null) {
                    // Get contract quantity for this category from contractData
                    const categoryIdStr = String(categoryId);
                    let contractQty = 0;
                    
                    // Try to get from category data first
                    if (this.contractData && this.contractData.machinesByCategory) {
                        const category = this.contractData.machinesByCategory.find(cat => String(cat.category_id) === categoryIdStr);
                        if (category && category.contract_quantity) {
                            contractQty = category.contract_quantity;
                        }
                    }
                    
                    // Fallback: get from first machine in category if category data not available
                    if (contractQty === 0) {
                        const firstMachineInCategory = this.addedMachines.find(m => 
                            m.machine_category_id == categoryId && m.contractQuantityPerCategory > 0
                        );
                        if (firstMachineInCategory) {
                            contractQty = firstMachineInCategory.contractQuantityPerCategory;
                        }
                    }
                    
                    if (contractQty === 0) {
                        return 0;
                    }
                    
                    // Get used quantity from existing PIs for this contract
                    const usedFromExistingPIs = this.usedQuantitiesByCategory[categoryIdStr] || 0;
                    
                    // Get used quantity from current form (other machines in this category)
                    const usedInCurrentForm = this.getTotalQuantityUsedPerCategory(categoryId, excludeTempId);
                    
                    // Total used = existing PIs + current form
                    const totalUsed = usedFromExistingPIs + usedInCurrentForm;
                    
                    return Math.max(0, contractQty - totalUsed);
                },

                updateAvailableQuantity(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !machine.machine_category_id) {
                        return;
                    }
                    
                    // Get available quantity for this category (excluding current machine)
                    const availableQty = this.getAvailableQuantityForCategory(machine.machine_category_id, tempId);
                    
                    // Update maxQuantity to available quantity
                    machine.maxQuantity = availableQty;
                    
                    // If current quantity exceeds available, reset it
                    if (machine.quantity > availableQty) {
                        machine.quantity = 0;
                    }
                    
                    // Update all other machines in the same category
                    this.addedMachines.forEach(m => {
                        if (m.machine_category_id == machine.machine_category_id && m.tempId !== tempId && m.contract_machine_id) {
                            const otherAvailableQty = this.getAvailableQuantityForCategory(m.machine_category_id, m.tempId);
                            m.maxQuantity = otherAvailableQty;
                            if (m.quantity > otherAvailableQty) {
                                m.quantity = Math.max(0, otherAvailableQty);
                            }
                        }
                    });
                },

                updateMachineQuantity(tempId, unitAmount, quantity) {
                    quantity = parseInt(quantity) || 0;
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    
                    if (!machine) return;
                    
                    // Update available quantity first
                    this.updateAvailableQuantity(tempId);
                    
                    // Check against available quantity
                    if (machine.maxQuantity !== undefined && quantity > machine.maxQuantity) {
                        const usedFromExistingPIs = this.usedQuantitiesByCategory[machine.machine_category_id] || 0;
                        const usedInCurrentForm = this.getTotalQuantityUsedPerCategory(machine.machine_category_id, tempId);
                        alert(`Quantity cannot exceed available quantity of ${machine.maxQuantity} for this category.\n\nContract Quantity: ${machine.contractQuantityPerCategory}\nUsed in existing PIs: ${usedFromExistingPIs}\nUsed in this form: ${usedInCurrentForm}\nAvailable: ${machine.maxQuantity}`);
                        machine.quantity = 0;
                        quantity = 0;
                        return;
                    }

                    machine.quantity = quantity;
                    
                    // Update available quantities for all machines in same category
                    this.updateAvailableQuantity(tempId);
                    
                    this.calculateTotal();
                },

                getUsdRateMultiplier() {
                    if (this.typeOfSale === 'local') {
                        const rate = parseFloat(this.usdRate || 0);
                        return rate > 0 ? rate : 1;
                    }
                    return 1;
                },

                toSaleCurrency(amount) {
                    const value = parseFloat(amount || 0);
                    return value * this.getUsdRateMultiplier();
                },

                getMachineDisplayAmount(tempId, unitAmountUSD) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    
                    const quantity = machine.quantity || 0;
                    const piPrice = parseFloat(unitAmountUSD || machine.amount || 0);
                    const amcPrice = parseFloat(machine.amc_price) || 0;                   
                    let amount = (quantity * piPrice) + amcPrice;
                    
                    return this.toSaleCurrency(amount);
                },

                calculateTotal() {
                  
                    this.totalAmount = this.addedMachines.reduce((sum, machine) => {
                        const quantity = machine.quantity || 0;
                        const piPrice = machine.amount || 0;
                        const amcPrice = machine.amc_price || 0;
                        const piMachineAmount = quantity * piPrice;
                        return sum + piMachineAmount + amcPrice;
                    }, 0);
                    
                    // Calculate final amounts including expenses and GST
                    // Note: displayTotalAmount will be set in calculateFinalAmounts()
                    this.calculateFinalAmounts();
                },

                calculateFinalAmounts() {
                    // Sum all machine final amounts in USD (before any currency conversion)
                    // Each machine final amount includes: PI + AMC + Commission + Freight + Port Exp + GST
                    let totalFinalAmountUSD = this.addedMachines.reduce((sum, machine) => {
                        const piMachineAmount = (machine.amount || 0) * (machine.quantity || 0);
                        const amcPrice = parseFloat(machine.amc_price) || 0;
                        const piTotalAmount = piMachineAmount + amcPrice;
                        const commissionAmount = this.getMachineCommissionAmount(machine.tempId);
                        const overseasFreight = parseFloat(machine.overseas_freight) || 0;
                        const portExpenses = parseFloat(machine.port_expenses_clearing) || 0;
                        const gstAmount = this.getMachineGSTAmount(machine.tempId);
                        return sum + piTotalAmount + commissionAmount + overseasFreight + portExpenses + gstAmount;
                    }, 0);
                    
                    // Store USD amount for display in parentheses
                    this.finalAmountWithGST = totalFinalAmountUSD;
                    
                    // Set display amount based on type of sale
                    if (this.typeOfSale === 'local') {
                        this.displayTotalAmount = this.toSaleCurrency(totalFinalAmountUSD);
                    } else {
                        this.displayTotalAmount = totalFinalAmountUSD;
                    }
                    
                    // Also update totalAmount for button disabled condition
                    this.totalAmount = totalFinalAmountUSD;
                },

                calculateMachineTotals(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        // Calculate PI Machine Amount + AMC Price
                        // PI Machine Amount = PI Per Machine Amount × Quantity
                        // AMC Price = AMC Price (not multiplied by quantity)
                        const piMachineAmount = (machine.amount || 0) * (machine.quantity || 0);
                        const amcPrice = parseFloat(machine.amc_price) || 0;
                        machine.pi_price_plus_amc = piMachineAmount + amcPrice;
                        
                        // Store total for reference
                        machine.total_pi_price = machine.pi_price_plus_amc;
                    }
                    this.calculateTotal();
                },

                getMachineCommissionAmount(tempId) {
                    // Only calculate commission for High Seas sales
                    if (this.typeOfSale !== 'high_seas') return 0;
                    
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    
                    // PI-AMC Amount
                    const piMachineAmount = (machine.amount || 0) * (machine.quantity || 0);
                    const amcPrice = parseFloat(machine.amc_price) || 0;
                    const piAmcAmount = piMachineAmount + amcPrice;
                    
                    // Commission Amount = High Seas Commission (%) × PI-AMC Amount
                    const commissionPercent = parseFloat(this.commission) || 0;
                    return (piAmcAmount * commissionPercent) / 100;
                },

                getMachineGSTAmount(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    
                    // Get GST percentage - use machine level if set and > 0, otherwise use PI-level
                    let gstPercent = 0;
                    if (machine.gst_percentage !== undefined && machine.gst_percentage !== null && machine.gst_percentage !== '' && parseFloat(machine.gst_percentage) > 0) {
                        gstPercent = parseFloat(machine.gst_percentage);
                    } else if (this.gstPercentage !== undefined && this.gstPercentage !== null && this.gstPercentage !== '' && parseFloat(this.gstPercentage) > 0) {
                        gstPercent = parseFloat(this.gstPercentage);
                    }
                    
                    // If GST percentage is 0 or not set, return 0
                    if (gstPercent <= 0) return 0;
                    
                    // PI Machine Amount + AMC Price
                    // PI Machine Amount = PI Per Machine Amount × Quantity
                    // AMC Price = AMC Price (not multiplied by quantity)
                    const piMachineAmount = (machine.amount || 0) * (machine.quantity || 0);
                    const amcPrice = parseFloat(machine.amc_price) || 0;
                    const piTotalAmount = piMachineAmount + amcPrice;
                    
                    // GST Amount = (PI Machine Amount + AMC Price) × GST Per
                    return (piTotalAmount * gstPercent) / 100;
                },

                getMachineFinalAmount(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    
                    // PI Machine Amount + AMC Price
                    // PI Machine Amount = PI Per Machine Amount × Quantity
                    // AMC Price = AMC Price (not multiplied by quantity)
                    const piMachineAmount = (machine.amount || 0) * (machine.quantity || 0);
                    const amcPrice = parseFloat(machine.amc_price) || 0;
                    const piTotalAmount = piMachineAmount + amcPrice;
                    
                    // Commission Amount (for High Seas only)
                    const commissionAmount = this.getMachineCommissionAmount(tempId);
                    
                    // Overseas Freight
                    const overseasFreight = parseFloat(machine.overseas_freight) || 0;
                    
                    // Port Exp + Clearing Exp
                    const portExpenses = parseFloat(machine.port_expenses_clearing) || 0;
                    
                    // GST Amount
                    const gstAmount = this.getMachineGSTAmount(tempId);
                    
                    // Final Amount = PI Machine Amount + AMC Price + Commission + Overseas Freight + Port Exp + GST
                    let finalAmount = piTotalAmount + commissionAmount + overseasFreight + portExpenses + gstAmount;
                    
                    return finalAmount;
                },

                calculateMachineFinalAmount(tempId) {
                    // Trigger recalculation for this machine
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        // Force reactivity by accessing the calculated values
                        this.getMachineCommissionAmount(tempId);
                        this.getMachineGSTAmount(tempId);
                        this.getMachineFinalAmount(tempId);
                    }
                    this.calculateTotal();
                },

                updateAllMachineCalculations() {
                    // Recalculate all machines when commission changes
                    this.addedMachines.forEach(machine => {
                        if (machine.contract_machine_id) {
                            this.getMachineCommissionAmount(machine.tempId);
                            this.getMachineFinalAmount(machine.tempId);
                        }
                    });
                    this.calculateTotal();
                },

                copyBillingAddressToShipping() {
                    if (this.copyBillingToShipping) {
                        this.shippingAddress = this.billingAddress;
                    }
                },

                toggleCategory(categoryId) {
                    const index = this.selectedCategories.indexOf(categoryId);
                    if (index > -1) {
                        this.selectedCategories.splice(index, 1);
                    } else {
                        this.selectedCategories.push(categoryId);
                    }
                },

                isCategorySelected(categoryId) {
                    return this.selectedCategories.includes(categoryId);
                },

                getSelectedCategories() {
                    if (!this.contractData || !this.contractData.machinesByCategory) {
                        return [];
                    }
                    return this.contractData.machinesByCategory.filter(category => 
                        this.selectedCategories.includes(category.category_id)
                    );
                },

                getMachineIndex(categoryId, machineIndex) {
                    let index = 0;
                    for (let cat of this.getSelectedCategories()) {
                        if (cat.category_id == categoryId) {
                            return index + machineIndex;
                        }
                        index += cat.machines.length;
                    }
                    return index;
                },

                removeMachineFromSelection(machineId) {
                    // Remove machine from selected categories by unselecting its category if it's the only machine
                    // For now, just remove from selectedMachines
                    delete this.selectedMachines[machineId];
                    this.calculateTotal();
                },

                updateMachineAmount(tempId, amount) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        machine.amount = parseFloat(amount) || 0;
                        this.calculateTotal();
                    }
                },

                getMachineEditableAmount(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    return this.toSaleCurrency(machine.amount || 0);
                },

                updateMachineAmountFromDisplay(tempId, displayAmount) {
                    const multiplier = this.getUsdRateMultiplier();
                    const displayValue = parseFloat(displayAmount) || 0;
                    const baseAmount = displayValue / (multiplier || 1);
                    this.updateMachineAmount(tempId, baseAmount);
                },

                cancelSelection() {
                    // Redirect back to the PI view page
                    window.location.href = '{{ route("proforma-invoices.show", $proformaInvoice) }}';
                },

                submitForm(event) {
                    // Ensure final amounts are calculated before submission
                    this.calculateFinalAmounts();
                    
                    // Remove required attribute from hidden select fields to prevent validation errors
                    const form = document.getElementById('proformaInvoiceForm');
                    if (form) {
                        const hiddenSelects = form.querySelectorAll('select[name*="[machine_category_id]"]');
                        hiddenSelects.forEach(select => {
                            if (select.style.display === 'none' || select.offsetParent === null) {
                                select.removeAttribute('required');
                            }
                        });
                    }
                    
                    // Check if form is valid
                    if (this.displayTotalAmount <= 0) {
                        event.preventDefault();
                        alert('Please add at least one machine with a valid amount.');
                        return false;
                    }
                    
                    if (!this.typeOfSale) {
                        event.preventDefault();
                        alert('Please select a Type of Sale.');
                        return false;
                    }
                    
                    if (!this.sellerId) {
                        event.preventDefault();
                        alert('Please select a Seller.');
                        return false;
                    }
                    
                    if (!this.addedMachines || this.addedMachines.length === 0) {
                        event.preventDefault();
                        alert('Please add at least one machine.');
                        return false;
                    }
                    
                    // Validate that all visible machines have category selected
                    for (let i = 0; i < this.addedMachines.length; i++) {
                        const machine = this.addedMachines[i];
                        if (!machine.machine_category_id || machine.machine_category_id === '') {
                            event.preventDefault();
                            alert(`Please select a machine category for Machine ${i + 1}.`);
                            return false;
                        }
                    }
                    
                    // If all validations pass, allow form to submit naturally
                    return true;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>