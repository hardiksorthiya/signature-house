<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg-auto order-lg-0">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Proforma Invoices Management</h1>
                <p class="text-muted mb-0 small">View and manage all proforma invoices with their details</p>
            </div>
            @can('create proforma invoices')
            <div class="col-12 col-lg order-lg-1">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    <a href="{{ route('proforma-invoices.create') }}" class="btn btn-success d-flex align-items-center shadow-sm">
                        <i class="fas fa-file-invoice me-1 me-sm-2"></i><span class="d-none d-sm-inline">Create Proforma Invoice</span><span class="d-inline d-sm-none">Create PI</span>
                    </a>
                </div>
            </div>
            @endcan
        </div>
    </div>

    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('proforma-invoices.index') }}" id="piFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="PI number, customer, contract..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                    <select name="sales_manager" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Sales Managers</option>
                        @foreach($salesManagers as $manager)
                            <option value="{{ $manager->id }}" {{ request('sales_manager') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Contract Number</label>
                    <input type="text" name="contract_number" value="{{ request('contract_number') }}" class="form-control" placeholder="Contract number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ request('customer_name') }}" class="form-control" placeholder="Customer name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-invoice text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="PI List">PI List</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $proformaInvoices->total() }} Total</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                        @if(request()->hasAny(['search', 'sales_manager', 'contract_number', 'customer_name']) && (request('search') || request('sales_manager') || request('contract_number') || request('customer_name')))
                            <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('proforma-invoices.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search PI, customer, contract..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    @if(request('sales_manager'))<input type="hidden" name="sales_manager" value="{{ request('sales_manager') }}">@endif
                    @if(request('contract_number'))<input type="hidden" name="contract_number" value="{{ request('contract_number') }}">@endif
                    @if(request('customer_name'))<input type="hidden" name="customer_name" value="{{ request('customer_name') }}">@endif
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                     <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Sr. No</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Buyer Company</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Seller</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Total Amount</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Type of Sale</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Client Status</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proformaInvoices as $pi)
                            <tr class="border-bottom">
                                <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $proformaInvoices->firstItem() + $loop->index }}</span></td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $pi->contract->contract_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $pi->buyer_company_name }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $pi->seller->seller_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-semibold" style="color: var(--primary-color);">
                                        @php
                                            // Match frontend logic exactly
                                            // Frontend for local: displayTotalAmount = totalFinalAmountUSD (USD amount, shown with ₹ symbol)
                                            // Frontend for high seas/import: displayTotalAmount = totalFinalAmountUSD (USD amount, shown with $ symbol)
                                            
                                            // Recalculate USD amount from machines (before currency conversion)
                                            if (!$pi->relationLoaded('proformaInvoiceMachines')) {
                                                $pi->load('proformaInvoiceMachines');
                                            }
                                            
                                            $totalMachineAmount = 0;
                                            $totalCommissionAmount = 0;
                                            
                                            foreach ($pi->proformaInvoiceMachines as $machine) {
                                                $unitAmount = $machine->amount ?? 0;
                                                $amcPrice = $machine->amc_price ?? 0;
                                                $piMachineAmount = $unitAmount * ($machine->quantity ?? 0);
                                                $piTotalAmount = $piMachineAmount + $amcPrice;
                                                $totalMachineAmount += $piTotalAmount;
                                                
                                                // Commission Amount (for High Seas only, per machine)
                                                if ($pi->type_of_sale === 'high_seas' && $pi->commission) {
                                                    $commissionAmount = ($piTotalAmount * $pi->commission) / 100;
                                                    $totalCommissionAmount += $commissionAmount;
                                                }
                                            }
                                            
                                            // Add stored totals (calculated per-machine and summed)
                                            $overseasFreight = $pi->overseas_freight ?? 0;
                                            $portExpensesClearing = $pi->port_expenses_clearing ?? 0;
                                            $gstAmount = $pi->gst_amount ?? 0;
                                            
                                            // Final amount in USD (before currency conversion) - matches frontend totalFinalAmountUSD
                                            $totalFinalAmountUSD = $totalMachineAmount + $totalCommissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                                            
                                            // Local: edit form stores USD internally but displays INR (USD × rate), same as toSaleCurrency()
                                            $localMult = ($pi->type_of_sale === 'local')
                                                ? (floatval($pi->usd_rate ?? 0) > 0 ? floatval($pi->usd_rate) : 1.0)
                                                : 1.0;
                                            if ($pi->type_of_sale === 'local') {
                                                $displayAmount = (float) $totalFinalAmountUSD * $localMult;
                                                $currencySymbol = '₹';
                                            } else {
                                                $displayAmount = (float) $totalFinalAmountUSD;
                                                $currencySymbol = $pi->currency === 'INR' ? '₹' : '$';
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $currencySymbol }}{{ number_format($displayAmount, 2) }}</span>
                                            @if($pi->type_of_sale === 'local' && floatval($pi->usd_rate ?? 0) > 0)
                                                <!-- USD equivalent for local: INR display / rate = stored USD total -->
                                                <span class="text-success" style="font-size: 0.875rem;">
                                                    (${{ number_format($displayAmount / floatval($pi->usd_rate), 2) }})
                                                </span>
                                            @elseif($pi->type_of_sale === 'high_seas' && $pi->usd_rate)
                                                <!-- Show INR equivalent for High Seas (Final Amount * USD Rate) -->
                                                <span class="text-success" style="font-size: 0.875rem;">
                                                    (₹{{ number_format($displayAmount * $pi->usd_rate, 2) }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2">
                                    @if($pi->type_of_sale)
                                        <span class="badge bg-info text-capitalize">{{ str_replace('_', ' ', $pi->type_of_sale) }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    @if($pi->contract && $pi->contract->approval_status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Confirmed
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Not Confirmed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <div class="d-flex gap-2" role="group">
                                        @can('view proforma invoices')
                                        <a href="{{ route('proforma-invoices.show', $pi) }}" class="btn btn-sm btn-outline-info" title="View PI Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('proforma-invoices.download-pdf', $pi) }}" class="btn btn-sm btn-outline-success" title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @endcan
                                        @can('edit proforma invoices')
                                        <a href="{{ route('proforma-invoices.edit', $pi) }}" class="btn btn-sm btn-outline-secondary" title="Edit PI">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete proforma invoices')
                                        <form action="{{ route('proforma-invoices.destroy', $pi) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete PI">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file-invoice fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No proforma invoices found.</p>
                                        <span class="text-muted mt-1 d-block">Create your first proforma invoice to get started</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($proformaInvoices->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $proformaInvoices->firstItem() ?? 0 }} to {{ $proformaInvoices->lastItem() ?? 0 }} of {{ $proformaInvoices->total() }} proforma invoices
                    </div>
                    <div>
                        {{ $proformaInvoices->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted text-center">
                    Showing {{ $proformaInvoices->count() }} of {{ $proformaInvoices->total() }} proforma invoices
                </div>
            </div>
        @endif
    </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .list-card { min-width: 0; }
        .list-header { flex-wrap: wrap; }
        .list-header-title-row { min-width: 0; }
        .list-header-search { min-width: 200px; }
        .filter-sidebar { width: 350px; max-width: 100%; }
        .pi-table th, .pi-table td {
            font-size: 0.9rem !important;
            line-height: 1.25 !important;
            padding-top: .35rem !important;
            padding-bottom: .35rem !important;
            padding-left: .6rem !important;
            padding-right: .6rem !important;
        }
        @media (max-width: 767.98px) {
            .filter-sidebar { width: 100% !important; }
        }
        @media (min-width: 992px) {
            .list-header-search { min-width: 240px; max-width: 360px; }
        }
    </style>

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
