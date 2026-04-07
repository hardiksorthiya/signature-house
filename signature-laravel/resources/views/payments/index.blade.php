<x-app-layout>
    <div x-data="paymentListFilter()" x-init="init()">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg-auto order-lg-0">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Payment Management</h1>
                <p class="text-muted mb-0 small">View and manage all collected and returned payments</p>
            </div>
            <div class="col-12 col-lg order-lg-1">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    @php $salesManager = request('sales_manager'); @endphp
                    <a href="{{ route('payments.collect-payment') }}{{ $salesManager ? ('?sales_manager=' . urlencode($salesManager)) : '' }}"
                       class="btn btn-success d-flex align-items-center shadow-sm">
                        <i class="fas fa-plus me-1 me-sm-2"></i>Add Payment
                    </a>
                    <a href="{{ route('payments.return-payment') }}{{ $salesManager ? ('?sales_manager=' . urlencode($salesManager)) : '' }}"
                       class="btn btn-danger d-flex align-items-center shadow-sm">
                        <i class="fas fa-undo me-1 me-sm-2"></i>Return Payment
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter sidebar (same as PI list) --}}
    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('payments.index') }}" id="paymentFilterForm">
                <input type="hidden" name="type" x-model="formType">
                <input type="hidden" name="sales_manager" x-model="formSalesManagerId">
                <input type="hidden" name="contract_number" x-model="formContractNumber">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Payment Type</label>
                    <select x-model="formType" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Payments</option>
                        <option value="collect">Collect Payment</option>
                        <option value="return">Return Payment</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                    <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                        <button type="button"
                                @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; contractDropdownOpen = false"
                                class="form-control text-start d-flex justify-content-between align-items-center"
                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                            <span x-text="formSalesManagerId ? salesManagers.find(m => m.id == formSalesManagerId)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
                            <i class="fas fa-chevron-down" :class="{ 'rotate-180': salesManagerDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                        </button>
                        <div x-show="salesManagerDropdownOpen"
                             x-cloak
                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                             style="z-index: 1060; max-height: 260px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                             @click.stop>
                            <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                <input type="text" x-model="salesManagerSearch" @click.stop placeholder="Search sales manager..."
                                       class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>
                            <template x-if="filteredSalesManagers.length === 0">
                                <div class="p-3 text-center text-muted small">No sales managers found</div>
                            </template>
                            <template x-for="m in filteredSalesManagers" :key="m.id">
                                <div class="d-flex align-items-center py-2 px-3"
                                     @click="selectSalesManager(m.id)"
                                     style="cursor: pointer;"
                                     :class="{ 'text-white': formSalesManagerId == m.id }"
                                     :style="formSalesManagerId == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                     onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                     onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                    <div class="fw-medium" x-text="m.name"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Contract Number</label>
                    <div class="position-relative" @click.away="contractDropdownOpen = false">
                        <button type="button"
                                @click="contractDropdownOpen = !contractDropdownOpen; salesManagerDropdownOpen = false"
                                class="form-control text-start d-flex justify-content-between align-items-center"
                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                :disabled="contracts.length === 0">
                            <span x-text="formContractNumber || 'Select Contract Number'"></span>
                            <i class="fas fa-chevron-down" :class="{ 'rotate-180': contractDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                        </button>
                        <div x-show="contractDropdownOpen"
                             x-cloak
                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                             style="z-index: 1060; max-height: 260px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                             @click.stop>
                            <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                <input type="text" x-model="contractSearch" @click.stop placeholder="Search contract number..."
                                       class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>
                            <template x-if="filteredContracts.length === 0">
                                <div class="p-3 text-center text-muted small">Select a sales manager first or no contracts found.</div>
                            </template>
                            <template x-for="c in filteredContracts" :key="c.id">
                                <div class="d-flex align-items-center py-2 px-3"
                                     @click="selectContract(c.contract_number)"
                                     style="cursor: pointer;"
                                     :class="{ 'text-white': formContractNumber == c.contract_number }"
                                     :style="formContractNumber == c.contract_number ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                     onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                     onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium" x-text="c.contract_number"></div>
                                        <small class="d-block" x-text="(c.buyer_name || '') + (c.company_name ? ' (' + c.company_name + ')' : '')"></small>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Customer Name</label>
                    <input type="text" name="customer_name" x-model="formCustomerName" class="form-control"
                           placeholder="Customer name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- List card (same header layout as PI list) --}}
    <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-money-bill-wave text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;">Payment List</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $payments->total() }} Total</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                        @if(request()->hasAny(['search', 'type', 'sales_manager', 'contract_number', 'customer_name']) && (request('search') || request('type') || request('sales_manager') || request('contract_number') || request('customer_name')))
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('payments.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search payment, contract, PI, customer..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
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
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Payment Type</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Payment Date</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Customer Name</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Payment Method</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Created By</th>
                            <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="border-bottom">
                                <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $payments->firstItem() + $loop->index }}</span></td>
                                <td class="px-2">
                                    @if($payment->type === 'collect')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Collect
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-undo me-1"></i>Return
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $payment->payment_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">
                                        {{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? 'N/A') }}
                                    </div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $payment->proformaInvoice->proforma_invoice_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">
                                        {{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? 'N/A') }}
                                        @if($payment->contract && $payment->contract->company_name)
                                            <br><small class="text-muted">({{ $payment->contract->company_name }})</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-semibold" style="{{ $payment->type === 'collect' ? 'color: #10b981;' : 'color: #dc2626;' }}">
                                        {{ $payment->payeeCountry && $payment->payeeCountry->currency ? $payment->payeeCountry->currency : '₹' }}{{ number_format($payment->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-2">
                                    <span class="badge bg-info text-capitalize">{{ $payment->payment_method ?? 'N/A' }}</span>
                                </td>
                                <td class="px-2">
                                    <small class="text-muted">{{ $payment->creator->name ?? 'N/A' }}</small>
                                </td>
                                <td class="px-2">
                            <div class="d-flex gap-2" role="group">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-info" title="View Payment">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Payment">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('payments.download-pdf', $payment->id) }}" class="btn btn-sm btn-outline-success" title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Payment">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No payments found.</p>
                                        <small class="text-muted mt-1">Add your first payment or adjust filters</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($payments->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} payments
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $payments->count() }} of {{ $payments->total() }} payments
                </div>
            </div>
        @endif
    </div>
    </div>

    <script>
        function paymentListFilter() {
            return {
                filterSidebarOpen: false,
                formType: '{{ request('type') ?? '' }}',
                formSalesManagerId: '{{ request('sales_manager') ?? '' }}',
                formContractNumber: '{{ request('contract_number') ?? '' }}',
                formCustomerName: '{{ request('customer_name') ?? '' }}',
                salesManagerSearch: '',
                contractSearch: '',
                salesManagerDropdownOpen: false,
                contractDropdownOpen: false,
                salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
                contracts: [],

                get filteredSalesManagers() {
                    if (!this.salesManagerSearch) return this.salesManagers;
                    const search = this.salesManagerSearch.toLowerCase();
                    return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(search)));
                },
                get filteredContracts() {
                    if (!this.contractSearch) return this.contracts;
                    const search = this.contractSearch.toLowerCase();
                    return this.contracts.filter(c =>
                        (c.contract_number && c.contract_number.toLowerCase().includes(search)) ||
                        (c.buyer_name && c.buyer_name.toLowerCase().includes(search)) ||
                        (c.company_name && c.company_name.toLowerCase().includes(search))
                    );
                },

                selectSalesManager(id) {
                    this.formSalesManagerId = id || '';
                    this.salesManagerDropdownOpen = false;
                    this.formContractNumber = '';
                    this.loadContracts();
                },
                loadContracts() {
                    if (!this.formSalesManagerId) {
                        this.contracts = [];
                        return;
                    }
                    const url = '{{ route('payments.get-contracts') }}?sales_manager_id=' + encodeURIComponent(this.formSalesManagerId);
                    fetch(url)
                        .then(r => r.ok ? r.json() : [])
                        .then(data => { this.contracts = Array.isArray(data) ? data : []; })
                        .catch(() => { this.contracts = []; });
                },
                selectContract(contractNumber) {
                    this.formContractNumber = contractNumber || '';
                    this.contractDropdownOpen = false;
                },

                init() {
                    if (this.formSalesManagerId && this.contracts.length === 0) {
                        this.loadContracts();
                    }
                }
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .rotate-180 { transform: rotate(180deg); }
        .pi-table th, .pi-table td {
            font-size: 0.9rem !important;
            line-height: 1.25 !important;
            padding-top: .35rem !important;
            padding-bottom: .35rem !important;
            padding-left: .6rem !important;
            padding-right: .6rem !important;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .action-btn-view { border-color: #06b6d4; color: #06b6d4; }
        .action-btn-view:hover { background-color: #06b6d4; color: white; }
        .action-btn-edit { border-color: #800020; color: #800020; }
        .action-btn-edit:hover { background-color: #800020; color: white; }
        .action-btn-pdf { border-color: #10b981; color: #10b981; }
        .action-btn-pdf:hover { background-color: #10b981; color: white; }
        .action-btn-delete { border-color: #dc2626; color: #dc2626; }
        .action-btn-delete:hover { background-color: #dc2626; color: white; }
    </style>
</x-app-layout>
