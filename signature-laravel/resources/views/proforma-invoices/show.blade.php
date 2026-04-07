@php
use Illuminate\Support\Facades\Storage;
@endphp
<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Proforma Invoice Details</h1>
            <p class="text-muted mb-0">Proforma Invoice: {{ $proformaInvoice->proforma_invoice_number }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('view proforma invoices')
            <a href="{{ route('proforma-invoices.download-pdf', $proformaInvoice) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            @endcan
            @canany(['view contract approvals', 'convert contract'])
            <a href="{{ route('machine-statuses.create', ['proforma_invoice_id' => $proformaInvoice->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks me-2"></i>Status
            </a>
            @endcanany
            @can('edit proforma invoices')
            <a href="{{ route('proforma-invoices.edit', $proformaInvoice) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            
            @endcan
            @can('delete proforma invoices')
            <form action="{{ route('proforma-invoices.destroy', $proformaInvoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </form>
            @endcan
            <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to PI List
            </a>
           
        </div>
    </div>

    <div class="row g-4">
        <!-- Proforma Invoice Information Card -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Proforma Invoice Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Proforma Invoice Number</label>
                            <div class="fw-semibold" style="color: #1f2937;">{{ $proformaInvoice->proforma_invoice_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->contract->contract_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Customer Name</label>
                            <div class="fw-medium" style="color: #1f2937;">{{ $proformaInvoice->contract->buyer_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Company Name</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->contract->company_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Seller</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Type of Sale</label>
                            <div>
                                @if($proformaInvoice->type_of_sale)
                                    <span class="badge bg-info text-capitalize">{{ str_replace('_', ' ', $proformaInvoice->type_of_sale) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Currency</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->currency ?? 'USD' }}</div>
                        </div>
                        @if($proformaInvoice->type_of_sale === 'local' || $proformaInvoice->type_of_sale === 'high_seas')
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">USD Rate</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->usd_rate !== null && $proformaInvoice->usd_rate !== '' ? number_format((float) $proformaInvoice->usd_rate, 2) : '—' }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Amount</label>
                            <div class="fw-semibold text-primary" style="font-size: 1.125rem;">
                                @php
                                    // Match frontend logic: recalculate USD amount from machines (before currency conversion)
                                    if (!$proformaInvoice->relationLoaded('proformaInvoiceMachines')) {
                                        $proformaInvoice->load('proformaInvoiceMachines');
                                    }
                                    
                                    $totalMachineAmount = 0;
                                    $totalCommissionAmount = 0;
                                    
                                    foreach ($proformaInvoice->proformaInvoiceMachines as $machine) {
                                        $unitAmount = $machine->amount ?? 0;
                                        $amcPrice = $machine->amc_price ?? 0;
                                        $piMachineAmount = $unitAmount * ($machine->quantity ?? 0);
                                        $piTotalAmount = $piMachineAmount + $amcPrice;
                                        $totalMachineAmount += $piTotalAmount;
                                        
                                        // Commission Amount (for High Seas only, per machine)
                                        if ($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->commission) {
                                            $commissionAmount = ($piTotalAmount * $proformaInvoice->commission) / 100;
                                            $totalCommissionAmount += $commissionAmount;
                                        }
                                    }
                                    
                                    // Add stored totals (calculated per-machine and summed)
                                    $overseasFreight = $proformaInvoice->overseas_freight ?? 0;
                                    $portExpensesClearing = $proformaInvoice->port_expenses_clearing ?? 0;
                                    $gstAmount = $proformaInvoice->gst_amount ?? 0;
                                    
                                    // Final amount in USD (before currency conversion) - matches frontend totalFinalAmountUSD
                                    $totalFinalAmountUSD = $totalMachineAmount + $totalCommissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                                    $localMult = ($proformaInvoice->type_of_sale === 'local')
                                        ? (floatval($proformaInvoice->usd_rate ?? 0) > 0 ? floatval($proformaInvoice->usd_rate) : 1.0)
                                        : 1.0;
                                    if ($proformaInvoice->type_of_sale === 'local') {
                                        $displayAmount = (float) $totalFinalAmountUSD * $localMult;
                                        $currencySymbol = '₹';
                                    } else {
                                        $displayAmount = (float) $totalFinalAmountUSD;
                                        $currencySymbol = $proformaInvoice->currency === 'INR' ? '₹' : '$';
                                    }
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span>{{ $currencySymbol }}{{ number_format($displayAmount, 2) }}</span>
                                    @if($proformaInvoice->type_of_sale === 'local' && floatval($proformaInvoice->usd_rate ?? 0) > 0)
                                        <span class="text-success" style="font-size: 0.875rem;">
                                            (${{ number_format($displayAmount / floatval($proformaInvoice->usd_rate), 2) }})
                                        </span>
                                    @elseif($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->usd_rate)
                                        <!-- Show INR equivalent for High Seas -->
                                        <span class="text-success" style="font-size: 0.875rem;">
                                            (₹{{ number_format($totalFinalAmountUSD * floatval($proformaInvoice->usd_rate), 2) }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($proformaInvoice->billing_address)
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Billing Address</label>
                            <div style="color: #1f2937; white-space: pre-line;">{{ $proformaInvoice->billing_address }}</div>
                        </div>
                        @endif
                        @if($proformaInvoice->shipping_address)
                        <div class="col-md-6">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Shipping Address</label>
                            <div style="color: #1f2937; white-space: pre-line;">{{ $proformaInvoice->shipping_address }}</div>
                        </div>
                        @endif
                        @if($proformaInvoice->notes)
                        <div class="col-12">
                            <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Notes</label>
                            <div style="color: #1f2937;">{{ $proformaInvoice->notes }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Machine Details -->
                    @if($proformaInvoice->proformaInvoiceMachines->count() > 0)
                    @php
                        $piLocalMult = ($proformaInvoice->type_of_sale === 'local')
                            ? (floatval($proformaInvoice->usd_rate ?? 0) > 0 ? floatval($proformaInvoice->usd_rate) : 1.0)
                            : 1.0;
                        $currencySymbol = ($proformaInvoice->type_of_sale === 'local')
                            ? '₹'
                            : ($proformaInvoice->currency === 'INR' ? '₹' : '$');
                    @endphp
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Machine Details</h5>
                        @foreach($proformaInvoice->proformaInvoiceMachines as $index => $piMachine)
                            @php
                                $contractMachine = $piMachine->contractMachine;
                                $piMachineTotalUsd = (($piMachine->amount ?? 0) * ($piMachine->quantity ?? 0)) + ($piMachine->amc_price ?? 0);
                                $piMachineTotal = ($proformaInvoice->type_of_sale === 'local')
                                    ? $piMachineTotalUsd * $piLocalMult
                                    : $piMachineTotalUsd;
                            @endphp
                            <div class="card mb-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0" style="color: #1f2937;">
                                            Machine #{{ $index + 1 }}
                                            @if($contractMachine && $contractMachine->machineCategory)
                                                - {{ $contractMachine->machineCategory->name }}
                                            @endif
                                        </h6>
                                        <div class="fw-semibold text-primary">
                                            {{ $currencySymbol }}{{ number_format($piMachineTotal, 2) }}
                                        </div>
                                    </div>

                                    <div class="row g-2 small">
                                        @if($contractMachine && $contractMachine->brand)
                                            <div class="col-md-6"><strong>Brand:</strong> {{ $contractMachine->brand->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->seller)
                                            <div class="col-md-6"><strong>Machine Seller:</strong> {{ $contractMachine->seller->seller_name }}</div>
                                        @endif

                                       

                                        @if($contractMachine && $contractMachine->machineModel)
                                            <div class="col-md-6"><strong>Model:</strong> {{ $contractMachine->machineModel->model_no }}</div>
                                        @endif

                                        @if($piMachine->feeder)
                                            <div class="col-md-6">
                                                <strong>Feeder:</strong>
                                                {{ $piMachine->feeder->feeder }}
                                                {{ $piMachine->feeder->feederBrand ? ' (' . $piMachine->feeder->feederBrand->name . ')' : '' }}
                                            </div>
                                        @endif

                                        @if($contractMachine && $contractMachine->color)
                                            <div class="col-md-6"><strong>Color Selector:</strong> {{ $contractMachine->color->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineDropin)
                                            <div class="col-md-6"><strong>Dropins:</strong> {{ $contractMachine->machineDropin->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineBeam)
                                            <div class="col-md-6"><strong>Beam:</strong> {{ $contractMachine->machineBeam->name }}</div>
                                        @endif

                                        @if(!empty($contractMachine->machine_size_name))
                                            <div class="col-md-6"><strong>Machine Size:</strong> {{ $contractMachine->machine_size_name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineClothRoller)
                                            <div class="col-md-6"><strong>Cloth Roller:</strong> {{ $contractMachine->machineClothRoller->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineHook)
                                            <div class="col-md-6"><strong>Hooks:</strong> {{ $contractMachine->machineHook->hook }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineERead)
                                            <div class="col-md-6"><strong>E-Read:</strong> {{ $contractMachine->machineERead->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineNozzle)
                                            <div class="col-md-6"><strong>Nozzle:</strong> {{ $contractMachine->machineNozzle->nozzle }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineSoftware)
                                            <div class="col-md-6"><strong>Software:</strong> {{ $contractMachine->machineSoftware->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->hsnCode)
                                            <div class="col-md-6"><strong>HSN Code:</strong> {{ $contractMachine->hsnCode->hsn_code }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->wir)
                                            <div class="col-md-6"><strong>WIR:</strong> {{ $contractMachine->wir->wir }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineShaft)
                                            <div class="col-md-6"><strong>Shaft:</strong> {{ $contractMachine->machineShaft->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineLever)
                                            <div class="col-md-6"><strong>Lever:</strong> {{ $contractMachine->machineLever->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->machineChain)
                                            <div class="col-md-6"><strong>Chain:</strong> {{ $contractMachine->machineChain->name }}</div>
                                        @endif

                                        @if($piMachine->machineHealdWire)
                                            <div class="col-md-6"><strong>Heald Wires:</strong> {{ $piMachine->machineHealdWire->name }}</div>
                                        @endif

                                        @if($contractMachine && $contractMachine->deliveryTerm)
                                            <div class="col-md-6"><strong>Delivery Terms:</strong> {{ $contractMachine->deliveryTerm->name }}</div>
                                        @endif

                                        <div class="col-md-6"><strong>Quantity:</strong> {{ $piMachine->quantity }}</div>
                                        @php
                                            $unitUsd = (float) ($piMachine->amount ?? 0);
                                            $amcUsd = (float) ($piMachine->amc_price ?? 0);
                                            $unitDisplay = ($proformaInvoice->type_of_sale === 'local') ? $unitUsd * $piLocalMult : $unitUsd;
                                            $amcDisplay = ($proformaInvoice->type_of_sale === 'local') ? $amcUsd * $piLocalMult : $amcUsd;
                                        @endphp
                                        <div class="col-md-6"><strong>Price:</strong> {{ $currencySymbol }}{{ number_format($unitDisplay, 2) }}</div>

                                        @if(!empty($piMachine->amc_price) && floatval($piMachine->amc_price) > 0)
                                            <div class="col-md-6"><strong>AMC Price:</strong> {{ $currencySymbol }}{{ number_format($amcDisplay, 2) }}</div>
                                        @endif

                                        @if(!empty($piMachine->description))
                                            <div class="col-md-12"><strong>Description:</strong> {{ $piMachine->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Other Buyer Expenses Details -->
                    @php
                        $piLocalMultExp = ($proformaInvoice->type_of_sale === 'local')
                            ? (floatval($proformaInvoice->usd_rate ?? 0) > 0 ? floatval($proformaInvoice->usd_rate) : 1.0)
                            : 1.0;
                        $currencySymbol = ($proformaInvoice->type_of_sale === 'local')
                            ? '₹'
                            : ($proformaInvoice->currency === 'INR' ? '₹' : '$');
                    @endphp
                    @php
                        $hasOtherExpenses =
                            (!empty($proformaInvoice->overseas_freight) && floatval($proformaInvoice->overseas_freight) > 0)
                            || (!empty($proformaInvoice->port_expenses_clearing) && floatval($proformaInvoice->port_expenses_clearing) > 0)
                            || (!empty($proformaInvoice->gst_amount) && floatval($proformaInvoice->gst_amount) > 0)
                            || (!empty($proformaInvoice->gst_percentage) && floatval($proformaInvoice->gst_percentage) > 0)
                            || (!empty($proformaInvoice->commission) && floatval($proformaInvoice->commission) > 0);
                    @endphp
                    @if($hasOtherExpenses)
                        <div class="mt-4 pt-4 border-top">
                            <h5 class="fw-semibold mb-3" style="color: #1f2937;">Other Buyer Expenses Details</h5>
                            <div class="row g-3">
                                @if(!empty($proformaInvoice->overseas_freight) && floatval($proformaInvoice->overseas_freight) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Overseas Freight</label>
                                        <div style="color: #1f2937;">{{ $currencySymbol }}{{ number_format(floatval($proformaInvoice->overseas_freight) * ($proformaInvoice->type_of_sale === 'local' ? $piLocalMultExp : 1), 2) }}</div>
                                    </div>
                                @endif

                                @if(!empty($proformaInvoice->port_expenses_clearing) && floatval($proformaInvoice->port_expenses_clearing) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Port Exp + Clearing Exp</label>
                                        <div style="color: #1f2937;">{{ $currencySymbol }}{{ number_format(floatval($proformaInvoice->port_expenses_clearing) * ($proformaInvoice->type_of_sale === 'local' ? $piLocalMultExp : 1), 2) }}</div>
                                    </div>
                                @endif

                                @if($proformaInvoice->type_of_sale === 'high_seas' && !empty($proformaInvoice->commission) && floatval($proformaInvoice->commission) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">High Seas Commission (%)</label>
                                        <div style="color: #1f2937;">{{ number_format($proformaInvoice->commission, 2) }}%</div>
                                    </div>
                                @endif

                                @if(!empty($proformaInvoice->gst_percentage) && floatval($proformaInvoice->gst_percentage) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">GST Per</label>
                                        <div style="color: #1f2937;">{{ number_format($proformaInvoice->gst_percentage, 2) }}%</div>
                                    </div>
                                @endif

                                @if(!empty($proformaInvoice->gst_amount) && floatval($proformaInvoice->gst_amount) > 0)
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">GST Amount</label>
                                        <div style="color: #1f2937;">{{ $currencySymbol }}{{ number_format(floatval($proformaInvoice->gst_amount) * ($proformaInvoice->type_of_sale === 'local' ? $piLocalMultExp : 1), 2) }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contract Information Sidebar -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Contract Number</label>
                        <div style="color: #1f2937;">{{ $proformaInvoice->contract->contract_number }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Business Firm</label>
                        <div style="color: #1f2937;">{{ $proformaInvoice->contract->businessFirm->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Location</label>
                        <div style="color: #1f2937;">
                            {{ $proformaInvoice->contract->area->name ?? '' }}, 
                            {{ $proformaInvoice->contract->city->name ?? '' }}, 
                            {{ $proformaInvoice->contract->state->name ?? '' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium mb-1" style="color: #6b7280; font-size: 0.875rem;">Total Contract Amount</label>
                        <div class="fw-semibold text-primary">${{ number_format($proformaInvoice->contract->total_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('contracts.show', $proformaInvoice->contract) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>View Contract
                        </a>
                    </div>
                </div>
            </div>

          
        </div>
    </div>

    <!-- Delivery Details Section -->
    @if($proformaInvoice->deliveryDetails && $proformaInvoice->deliveryDetails->count() > 0)
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Delivery Details</h2>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('proforma-invoices.delivery-details-view', $proformaInvoice) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-file-alt me-2"></i>View delivery details
                            </a>
                            @can('edit proforma invoices')
                            <a href="{{ route('proforma-invoices.delivery-details', $proformaInvoice) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit delivery details
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 5%;" class="text-center">Status</th>
                                    <th style="width: 5%;" class="text-center">S.No</th>
                                    <th style="width: 25%;">Document Name</th>
                                    <th style="width: 18%;">Date</th>
                                    <th style="width: 22%;">Document Number</th>
                                    <th style="width: 15%;">No. of Copies</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proformaInvoice->deliveryDetails->sortBy('sort_order') as $index => $detail)
                                <tr>
                                    <td class="text-center">
                                        @if($detail->is_received)
                                            <span class="badge bg-success" title="Received">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary" title="Pending">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $detail->document_name }}</td>
                                    <td>{{ $detail->date ? $detail->date->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $detail->document_number ?? '-' }}</td>
                                    <td>{{ $detail->no_of_copies ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Uploaded Images Section -->
                    @if($proformaInvoice->documents && $proformaInvoice->documents->count() > 0)
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">
                            <i class="fas fa-images me-2"></i>Uploaded Images
                        </h5>
                        <div class="row g-3">
                            @foreach($proformaInvoice->documents as $image)
                            <div class="col-md-3">
                                <div class="card border position-relative">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         class="card-img-top" 
                                         style="height: 150px; object-fit: cover; cursor: pointer;" 
                                         alt="{{ $image->file_name }}"
                                         onclick="window.open('{{ Storage::url($image->file_path) }}', '_blank')"
                                         onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">
                                            {{ $image->file_name }}
                                        </small>
                                        <small class="text-muted">{{ number_format($image->file_size / 1024, 2) }} KB</small>
                                    </div>
                                    <a href="{{ Storage::url($image->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info position-absolute top-0 end-0 m-2" 
                                       title="View Full Size">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 px-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Delivery Details</h2>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('proforma-invoices.delivery-details-view', $proformaInvoice) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-file-alt me-2"></i>View delivery page
                            </a>
                            @can('edit proforma invoices')
                            <a href="{{ route('proforma-invoices.delivery-details', $proformaInvoice) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-2"></i>Add delivery details
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-0">No delivery documents have been saved for this PI yet. Use <strong>View delivery page</strong> for the full checklist, or <strong>Add delivery details</strong> to enter data.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>