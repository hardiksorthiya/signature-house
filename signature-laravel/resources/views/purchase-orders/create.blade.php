<x-app-layout>
    <div x-data="purchaseOrderForm({{ $selectedProformaInvoiceId ?? 'null' }}, '{{ $selectedSalesManagerId ?? '' }}')" x-init="init()">
        {{-- Page header: same layout as create proforma invoice --}}
        <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center flex-wrap gap-2" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-shopping-bag text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Create Purchase Order</h1>
                            <p class="text-muted mb-0 small">Select a proforma invoice, then fill in purchase order details</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-2 mt-lg-0">
                        <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-file-invoice me-2"></i>Proforma Invoices
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-list me-2"></i>Purchase Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Section: searchable dropdowns (same design as create proforma) --}}
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Search Proforma Invoice</h2>
                        <p class="text-muted small mb-0">Select sales manager, then search and select a proforma invoice</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                        <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                            <button type="button"
                                    @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; piDropdownOpen = false"
                                    class="form-control text-start d-flex justify-content-between align-items-center"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                <span x-text="selectedSalesManagerId ? salesManagers.find(m => m.id == selectedSalesManagerId)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
                                <i class="fas fa-chevron-down" :class="{ 'rotate-180': salesManagerDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                            </button>
                            <div x-show="salesManagerDropdownOpen"
                                 x-cloak
                                 class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                 style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                                 @click.stop>
                                <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                    <input type="text"
                                           x-model="salesManagerSearch"
                                           @click.stop
                                           placeholder="Search sales manager..."
                                           class="form-control form-control-sm"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <template x-if="filteredSalesManagers.length === 0">
                                    <div class="p-3 text-center text-muted small">No sales managers found</div>
                                </template>
                                <template x-for="m in filteredSalesManagers" :key="m.id">
                                    <div class="d-flex align-items-center py-2 px-3"
                                         @click="selectSalesManager(m.id)"
                                         style="cursor: pointer;"
                                         :class="{ 'text-white': selectedSalesManagerId == m.id }"
                                         :style="selectedSalesManagerId == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                         onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                         onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                        <div class="flex-grow-1">
                                            <div class="fw-medium" x-text="m.name"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium" style="color: #374151;">Proforma Invoice</label>
                        <div class="position-relative" @click.away="piDropdownOpen = false">
                            <button type="button"
                                    @click="piDropdownOpen = !piDropdownOpen; salesManagerDropdownOpen = false; if (piDropdownOpen && proformaInvoices.length === 0 && selectedSalesManagerId) { loadProformaInvoices(); }"
                                    class="form-control text-start d-flex justify-content-between align-items-center"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                    :disabled="proformaInvoices.length === 0">
                                <span x-text="selectedProformaInvoiceId ? (proformaInvoices.find(p => p.id == selectedProformaInvoiceId)?.proforma_invoice_number || 'Select Proforma Invoice') : 'Select Proforma Invoice'"></span>
                                <i class="fas fa-chevron-down" :class="{ 'rotate-180': piDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                            </button>
                            <div x-show="piDropdownOpen"
                                 x-cloak
                                 class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                 style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                                 @click.stop>
                                <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                    <input type="text"
                                           x-model="piSearch"
                                           @click.stop
                                           placeholder="Search PI number or buyer..."
                                           class="form-control form-control-sm"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <template x-if="filteredProformaInvoices.length === 0">
                                    <div class="p-3 text-center text-muted small">No proforma invoices found. Select a sales manager first or adjust your search.</div>
                                </template>
                                <template x-for="pi in filteredProformaInvoices" :key="pi.id">
                                    <div class="d-flex align-items-center py-2 px-3"
                                         @click="selectProformaInvoiceFromDropdown(pi.id)"
                                         style="cursor: pointer;"
                                         :class="{ 'text-white': selectedProformaInvoiceId == pi.id }"
                                         :style="selectedProformaInvoiceId == pi.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                         onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                         onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                        <div class="flex-grow-1">
                                            <div class="fw-medium" x-text="pi.proforma_invoice_number"></div>
                                            <small class="d-block" x-text="(pi.buyer_company_name || '') + (pi.contract?.contract_number ? ' • ' + pi.contract.contract_number : '')"></small>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Order Form (shown when PI is selected) -->
        @if($selectedProformaInvoiceId)
        @php
            $selectedPI = \App\Models\ProformaInvoice::with([
                'contract.creator',
                'contract.contractMachines.machineCategory',
                'contract.contractMachines.brand',
                'contract.contractMachines.machineModel.brand',
                'contract.contractMachines.machineSize',
                'contract.contractMachines.feeder.feederBrand',
                'contract.contractMachines.machineHook',
                'contract.contractMachines.machineERead',
                'contract.contractMachines.color',
                'contract.contractMachines.machineNozzle',
                'contract.contractMachines.machineDropin',
                'contract.contractMachines.machineBeam',
                'contract.contractMachines.machineClothRoller',
                'contract.contractMachines.machineSoftware',
                'contract.contractMachines.hsnCode',
                'contract.contractMachines.wir',
                'contract.contractMachines.machineShaft',
                'contract.contractMachines.machineLever',
                'contract.contractMachines.machineChain',
                'contract.contractMachines.machineHealdWire',
                'contract.contractMachines.deliveryTerm',
                'seller',
            ])->find($selectedProformaInvoiceId);
            $poMachineRows = $selectedPI && $selectedPI->contract
                ? $selectedPI->contract->contractMachines
                : collect();
        @endphp
        @if($selectedPI)
        <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-shopping-bag text-white"></i>
                    </div>
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Purchase Order Details</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data" id="poForm">
                    @csrf
                    <input type="hidden" name="proforma_invoice_id" value="{{ $selectedPI->id }}">

                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Basic Information</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="purchase_order_number" class="form-label fw-medium">PO Number <span class="text-danger">*</span></label>
                            <input type="text" name="purchase_order_number" id="purchase_order_number" 
                                   class="form-control @error('purchase_order_number') is-invalid @enderror" 
                                   value="{{ old('purchase_order_number', 'PO-' . date('Ymd') . '-' . str_pad($selectedPI->id, 4, '0', STR_PAD_LEFT)) }}" 
                                   required
                                   readonly
                                   style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #f3f4f6;">
                            @error('purchase_order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="buyer_name" class="form-label fw-medium">Buyer Name <span class="text-danger">*</span></label>
                            <input type="text" name="buyer_name" id="buyer_name" 
                                   class="form-control @error('buyer_name') is-invalid @enderror" 
                                   value="{{ old('buyer_name', $selectedPI->buyer_company_name) }}" 
                                   required
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('buyer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-medium">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Enter address"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address', $selectedPI->billing_address ?? $selectedPI->shipping_address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Machine Details from contract (source of truth; not PI snapshot) -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Machine Details (from Contract)</h5>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0; margin: 0;">
                                    <thead>
                                        <tr style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                                            <th class="px-4 py-3 text-white fw-semibold" style="border: none; min-width: 180px; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Machine Category</th>
                                            <th class="px-4 py-3 text-white fw-semibold" style="border: none; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Specifications</th>
                                            <th class="px-4 py-3 text-white fw-semibold text-center" style="border: none; min-width: 100px; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($poMachineRows as $index => $contractMachine)
                                            <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f8f9fa' }}; border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;">
                                                <td class="px-4 py-4" style="vertical-align: top; border: none;">
                                                    <div class="fw-semibold" style="font-size: 1.05rem; color: var(--primary-color) !important; line-height: 1.4;">
                                                        <i class="fas fa-cog me-2" style="color: var(--primary-color);"></i>{{ $contractMachine->machineCategory->name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4" style="vertical-align: top; border: none;">
                                                    <div class="specifications-container" style="display: flex; flex-wrap: wrap; gap: 12px;">
                                                        @php
                                                            $specs = [];
                                                            if($contractMachine->brand) $specs[] = ['label' => 'Brand', 'value' => $contractMachine->brand->name];
                                                            if($contractMachine->machineModel) $specs[] = ['label' => 'Model', 'value' => $contractMachine->machineModel->model_no . ($contractMachine->machineModel->brand ? ' (' . $contractMachine->machineModel->brand->name . ')' : '')];
                                                            if($contractMachine->machineSize) $specs[] = ['label' => 'Machine Size', 'value' => $contractMachine->machineSize->name];
                                                            if($contractMachine->feeder) $specs[] = ['label' => 'Feeder', 'value' => $contractMachine->feeder->feeder . ($contractMachine->feeder->feederBrand ? ' (' . $contractMachine->feeder->feederBrand->name . ')' : '')];
                                                            if($contractMachine->machineHook) $specs[] = ['label' => 'Hook', 'value' => $contractMachine->machineHook->hook];
                                                            if($contractMachine->machineERead) $specs[] = ['label' => 'E-Read', 'value' => $contractMachine->machineERead->name];
                                                            if($contractMachine->color) $specs[] = ['label' => 'Color', 'value' => $contractMachine->color->name];
                                                            if($contractMachine->machineNozzle) $specs[] = ['label' => 'Nozzle', 'value' => $contractMachine->machineNozzle->nozzle];
                                                            if($contractMachine->machineDropin) $specs[] = ['label' => 'Dropin', 'value' => $contractMachine->machineDropin->name];
                                                            if($contractMachine->machineBeam) $specs[] = ['label' => 'Beam', 'value' => $contractMachine->machineBeam->name];
                                                            if($contractMachine->machineClothRoller) $specs[] = ['label' => 'Cloth Roller', 'value' => $contractMachine->machineClothRoller->name];
                                                            if($contractMachine->machineSoftware) $specs[] = ['label' => 'Software', 'value' => $contractMachine->machineSoftware->name];
                                                            if($contractMachine->hsnCode) $specs[] = ['label' => 'HSN Code', 'value' => $contractMachine->hsnCode->name];
                                                            if($contractMachine->wir) $specs[] = ['label' => 'WIR', 'value' => $contractMachine->wir->name];
                                                            if($contractMachine->machineShaft) $specs[] = ['label' => 'Shaft', 'value' => $contractMachine->machineShaft->name];
                                                            if($contractMachine->machineLever) $specs[] = ['label' => 'Lever', 'value' => $contractMachine->machineLever->name];
                                                            if($contractMachine->machineChain) $specs[] = ['label' => 'Chain', 'value' => $contractMachine->machineChain->name];
                                                            if($contractMachine->machineHealdWire) $specs[] = ['label' => 'Heald Wire', 'value' => $contractMachine->machineHealdWire->name];
                                                            if($contractMachine->deliveryTerm) $specs[] = ['label' => 'Delivery Term', 'value' => $contractMachine->deliveryTerm->name];
                                                        @endphp
                                                        @foreach($specs as $spec)
                                                            <div class="spec-badge" style="display: inline-flex; align-items: center; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 12px; margin: 0;">
                                                                <span class="text-muted" style="font-size: 0.75rem; font-weight: 500; margin-right: 6px; text-transform: uppercase; letter-spacing: 0.3px;">{{ $spec['label'] }}:</span>
                                                                <span class="fw-medium" style="font-size: 0.875rem; color: #1f2937;">{{ $spec['value'] }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if($contractMachine->description)
                                                            <div class="spec-badge" style="display: inline-flex; align-items: start; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fbbf24; border-radius: 6px; padding: 8px 12px; margin: 0; width: 100%; margin-top: 8px;">
                                                                <span class="text-muted" style="font-size: 0.75rem; font-weight: 500; margin-right: 6px; text-transform: uppercase; letter-spacing: 0.3px;">Description:</span>
                                                                <span style="font-size: 0.875rem; color: #78350f; flex: 1;">{{ $contractMachine->description }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center" style="vertical-align: middle; border: none;">
                                                    <span class="badge rounded-pill px-3 py-2" style="font-size: 1rem; min-width: 50px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                        {{ $contractMachine->quantity }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="border: none;">
                                                    <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.2; color: #9ca3af;"></i>
                                                    <div style="font-size: 1rem;">No machines found on the linked contract</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                                Payment Details (First $ Transaction for this PI)
                                <button type="button" @click="loadPaymentDetails()" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-sync me-1"></i>Load Payment
                                </button>
                            </h5>
                        </div>

                        <div class="col-12" id="paymentDetailsSection" x-show="paymentDetails !== null" x-cloak>
                            <div class="alert alert-info">
                                <template x-if="paymentDetails">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <strong>Amount:</strong> <span x-text="paymentDetails.amount ? '$' + parseFloat(paymentDetails.amount).toFixed(2) : 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Date:</strong> <span x-text="paymentDetails.payment_date || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Method:</strong> <span x-text="paymentDetails.payment_method || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Transaction ID:</strong> <span x-text="paymentDetails.transaction_id || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payee Country:</strong> <span x-text="paymentDetails.payee_country || 'N/A'"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment To Seller:</strong> <span x-text="paymentDetails.payment_to_seller || 'N/A'"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!paymentDetails" class="text-muted">
                                    No payment transaction found with $ currency for this PI
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Details -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Shipping Details</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="no_of_bill" class="form-label fw-medium">No of Bill</label>
                            <input type="number" name="no_of_bill" id="no_of_bill" 
                                   class="form-control @error('no_of_bill') is-invalid @enderror" 
                                   value="{{ old('no_of_bill') }}" 
                                   min="0"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('no_of_bill')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="no_of_container" class="form-label fw-medium">No of Container</label>
                            <input type="number" name="no_of_container" id="no_of_container" 
                                   class="form-control @error('no_of_container') is-invalid @enderror" 
                                   value="{{ old('no_of_container') }}" 
                                   min="0"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('no_of_container')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="size_of_container" class="form-label fw-medium">Size of Container</label>
                            <input type="text" name="size_of_container" id="size_of_container" 
                                   class="form-control @error('size_of_container') is-invalid @enderror" 
                                   value="{{ old('size_of_container') }}" 
                                   placeholder="e.g., 20ft, 40ft"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('size_of_container')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="port_of_destination_id" class="form-label fw-medium">Port of Destination</label>
                            <select name="port_of_destination_id" id="port_of_destination_id" 
                                    class="form-select @error('port_of_destination_id') is-invalid @enderror"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <option value="">Select Port of Destination</option>
                                @foreach($portOfDestinations as $port)
                                    <option value="{{ $port->id }}" {{ old('port_of_destination_id') == $port->id ? 'selected' : '' }}>
                                        {{ $port->name }} @if($port->code)({{ $port->code }})@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('port_of_destination_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Attachments</h5>
                        </div>

                        <div class="col-12">
                            <label for="attachments" class="form-label fw-medium">Upload Files</label>
                            <input type="file" name="attachments[]" id="attachments" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <small class="text-muted">You can select multiple files. Accepted formats: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR (Max 10MB per file)</small>
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12 mt-4">
                            <label for="notes" class="form-label fw-medium">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Purchase Order
                                </button>
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endif
    </div>
</x-app-layout>

<script>
    function purchaseOrderForm(selectedPIId = null, selectedSalesManagerId = '') {
        return {
            selectedProformaInvoiceId: selectedPIId,
            selectedSalesManagerId: selectedSalesManagerId || '',
            paymentDetails: null,
            salesManagerSearch: '',
            piSearch: '',
            salesManagerDropdownOpen: false,
            piDropdownOpen: false,
            salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
            proformaInvoices: [],

            get filteredSalesManagers() {
                if (!this.salesManagerSearch) return this.salesManagers;
                const search = this.salesManagerSearch.toLowerCase();
                return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(search)));
            },
            get filteredProformaInvoices() {
                if (!this.piSearch) return this.proformaInvoices;
                const search = this.piSearch.toLowerCase();
                return this.proformaInvoices.filter(pi =>
                    (pi.proforma_invoice_number && pi.proforma_invoice_number.toLowerCase().includes(search)) ||
                    (pi.buyer_company_name && pi.buyer_company_name.toLowerCase().includes(search)) ||
                    (pi.contract && pi.contract.contract_number && pi.contract.contract_number.toLowerCase().includes(search))
                );
            },

            selectSalesManager(id) {
                this.selectedSalesManagerId = id || '';
                this.salesManagerDropdownOpen = false;
                this.loadProformaInvoices();
            },
            loadProformaInvoices() {
                if (!this.selectedSalesManagerId) {
                    this.proformaInvoices = [];
                    return;
                }
                const url = '{{ route('purchase-orders.get-pis-by-sales-manager') }}?sales_manager_id=' + encodeURIComponent(this.selectedSalesManagerId);
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to load');
                        return response.json();
                    })
                    .then(data => {
                        this.proformaInvoices = Array.isArray(data) ? data : [];
                    })
                    .catch(() => { this.proformaInvoices = []; });
            },
            selectProformaInvoiceFromDropdown(piId) {
                this.piDropdownOpen = false;
                const params = new URLSearchParams();
                params.set('proforma_invoice_id', piId);
                if (this.selectedSalesManagerId) params.set('sales_manager_id', this.selectedSalesManagerId);
                window.location.href = '{{ route('purchase-orders.create') }}?' + params.toString();
            },

            init() {
                if (this.selectedSalesManagerId && this.proformaInvoices.length === 0) {
                    this.loadProformaInvoices();
                }
                if (this.selectedProformaInvoiceId) {
                    setTimeout(() => this.loadPaymentDetails(), 100);
                }
            },

            loadPaymentDetails() {
                if (!this.selectedProformaInvoiceId) return;
                const url = '{{ url('/') }}' + '/purchase-orders/get-first-payment/' + this.selectedProformaInvoiceId;
                fetch(url)
                    .then(response => response.ok ? response.json() : { success: false })
                    .then(data => {
                        this.paymentDetails = (data.success && data.payment) ? data.payment : null;
                    })
                    .catch(() => { this.paymentDetails = null; });
            }
        };
    }
</script>
<style>
    [x-cloak] { display: none !important; }
    .rotate-180 { transform: rotate(180deg); }
</style>
