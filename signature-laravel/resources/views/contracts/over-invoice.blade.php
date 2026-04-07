<x-app-layout>
    <div x-data="overInvoiceSearch()" x-init="init()">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto order-lg-0">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Over Invoice Management</h1>
                    <p class="text-muted mb-0 small">Contracts where total Proforma Invoice amount exceeds contract amount</p>
                </div>
                
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search Section: searchable dropdowns (same design as proforma invoice create) --}}
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Search</h2>
                        <p class="text-muted small mb-0">Select a sales manager, then search by contract #, buyer, company, or PI number in one list</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('contracts.over-invoice') }}" id="overInvoiceSearchForm">
                    <input type="hidden" name="sales_manager" x-model="formSalesManager">
                    <input type="hidden" name="contract_number" x-model="formContractNumber">
                    <input type="hidden" name="customer_name" x-model="formCustomerName">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                            <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                                <button type="button"
                                        @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; unifiedDropdownOpen = false"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                    <span x-text="formSalesManager ? salesManagers.find(m => m.id == formSalesManager)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
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
                                             :class="{ 'text-white': formSalesManager == m.id }"
                                             :style="formSalesManager == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
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
                        <div class="col-lg-6">
                            <label class="form-label fw-medium" style="color: #374151;">Contract / customer / company / PI</label>
                            <div class="position-relative" @click.away="unifiedDropdownOpen = false">
                                <button type="button"
                                        @click="unifiedDropdownOpen = !unifiedDropdownOpen; salesManagerDropdownOpen = false; if (unifiedDropdownOpen && formSalesManager && contracts.length === 0) { loadContracts(); }"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!formSalesManager">
                                    <span class="text-truncate me-2" style="min-width: 0;" x-text="unifiedSelectedLabel"></span>
                                    <i class="fas fa-chevron-down flex-shrink-0" :class="{ 'rotate-180': unifiedDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                                </button>
                                <div x-show="unifiedDropdownOpen"
                                     x-cloak
                                     class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                     style="z-index: 1000; max-height: 320px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                                     @click.stop>
                                    <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                        <input type="text"
                                               x-model="unifiedSearch"
                                               @click.stop
                                               placeholder="Search contract number, buyer name, company name, PI number…"
                                               class="form-control form-control-sm"
                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                    <template x-if="filteredUnifiedContracts.length === 0">
                                        <div class="p-3 text-center text-muted small">Select a sales manager first, or no rows match your search.</div>
                                    </template>
                                    <template x-for="c in filteredUnifiedContracts" :key="c.id">
                                        <div class="d-flex align-items-center py-2 px-3"
                                             @click="selectUnified(c)"
                                             style="cursor: pointer;"
                                             :class="{ 'text-white': formContractNumber == c.contract_number }"
                                             :style="formContractNumber == c.contract_number ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                             onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                             onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-medium" x-text="c.contract_number"></div>
                                                <small class="d-block text-truncate" :class="formContractNumber == c.contract_number ? 'opacity-90' : 'text-muted'" x-text="unifiedRowSubtitle(c)"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <small class="text-muted">Choose one row; search matches any PI number on that contract.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('contracts.over-invoice') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Over Invoice Table Card (same list header pattern as PI / PO list) --}}
        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-exclamation-triangle text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Over Invoice List">Over Invoice List</h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $overInvoices->total() }} Total</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 w-100 mb-lg-0" style="max-width: 36rem;">Open <strong>View</strong> on a row to set the USD→INR rate and see difference in ₹.</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                         <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                            <tr>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Number</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer Name</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Amount ($)</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Amount ($)</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Difference ($)</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Difference (₹)</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overInvoices as $contract)
                                <tr class="border-bottom">
                                    <td class="px-2">
                                        <div class="fw-medium" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                                    </td>
                                    <td class="px-2">
                                        <div class="fw-medium" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                                        @if($contract->company_name)
                                            <small class="text-muted">{{ $contract->company_name }}</small>
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        <div class="fw-semibold" style="color: #059669;">
                                            ${{ number_format($contract->total_amount ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-2">
                                        <div class="fw-semibold" style="color: #2563eb;">
                                            ${{ number_format($contract->total_pi_amount ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-2">
                                        <div class="fw-semibold" style="color: #dc2626; font-size: 1.05rem;">
                                            ${{ number_format($contract->difference_amount ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-2">
                                        @if($contract->over_invoice_difference_inr !== null)
                                            <div class="fw-semibold" style="color: #059669; font-size: 1.05rem;">
                                                ₹{{ number_format((float) $contract->over_invoice_difference_inr, 2) }}
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        <div class="d-flex gap-2 flex-wrap" role="group">
                                            <a href="{{ route('contracts.over-invoice.show', $contract) }}" class="btn btn-sm btn-outline-info" title="View over invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-check-circle fa-3x mb-3" style="color: #10b981; opacity: 0.5;"></i>
                                            <p class="mb-0">No over invoices found.</p>
                                            <small class="text-muted mt-1">All contracts are within their proforma invoice limits</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($overInvoices->hasPages())
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $overInvoices->firstItem() ?? 0 }} to {{ $overInvoices->lastItem() ?? 0 }} of {{ $overInvoices->total() }} over invoices
                        </div>
                        <div>{{ $overInvoices->links() }}</div>
                    </div>
                </div>
            @elseif($overInvoices->count() > 0)
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="text-muted small text-center">
                        Showing {{ $overInvoices->total() }} over invoices
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function overInvoiceSearch() {
            return {
                formSalesManager: '{{ request('sales_manager') ?? '' }}',
                formContractNumber: '{{ request('contract_number') ?? '' }}',
                formCustomerName: '{{ request('customer_name') ?? '' }}',
                salesManagerSearch: '',
                unifiedSearch: '',
                salesManagerDropdownOpen: false,
                unifiedDropdownOpen: false,
                salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
                contracts: [],

                unifiedRowSubtitle(c) {
                    const parts = [];
                    if (c.buyer_name) parts.push(c.buyer_name);
                    if (c.company_name) parts.push(c.company_name);
                    if (c.pi_numbers && c.pi_numbers.length) {
                        parts.push('PI: ' + c.pi_numbers.join(', '));
                    }
                    return parts.join(' · ') || '—';
                },

                get unifiedSelectedLabel() {
                    if (this.formContractNumber) {
                        const c = this.contracts.find(x => x.contract_number === this.formContractNumber);
                        if (c) {
                            return c.contract_number + ' — ' + this.unifiedRowSubtitle(c);
                        }
                        return this.formContractNumber;
                    }
                    if (this.formCustomerName) {
                        return this.formCustomerName;
                    }
                    return 'Search contract number, buyer name, company name, PI number…';
                },

                get filteredSalesManagers() {
                    if (!this.salesManagerSearch) return this.salesManagers;
                    const search = this.salesManagerSearch.toLowerCase();
                    return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(search)));
                },

                get filteredUnifiedContracts() {
                    if (!this.unifiedSearch || !String(this.unifiedSearch).trim()) {
                        return this.contracts;
                    }
                    const search = String(this.unifiedSearch).toLowerCase().trim();
                    return this.contracts.filter(c => {
                        if (c.contract_number && String(c.contract_number).toLowerCase().includes(search)) return true;
                        if (c.buyer_name && String(c.buyer_name).toLowerCase().includes(search)) return true;
                        if (c.company_name && String(c.company_name).toLowerCase().includes(search)) return true;
                        if (c.pi_numbers && Array.isArray(c.pi_numbers)) {
                            return c.pi_numbers.some(p => p && String(p).toLowerCase().includes(search));
                        }
                        return false;
                    });
                },

                selectSalesManager(id) {
                    this.formSalesManager = id !== undefined && id !== null ? String(id) : '';
                    this.salesManagerDropdownOpen = false;
                    this.contracts = [];
                    this.unifiedSearch = '';
                    this.formContractNumber = '';
                    this.formCustomerName = '';
                    this.loadContracts();
                },

                loadContracts() {
                    const self = this;
                    if (!self.formSalesManager) {
                        self.contracts = [];
                        return;
                    }
                    const url = '{{ route('contracts.over-invoice.get-contracts-by-sales-manager') }}?sales_manager_id=' + encodeURIComponent(String(self.formSalesManager));
                    fetch(url)
                        .then(function(response) {
                            if (!response.ok) throw new Error('Failed to load');
                            return response.json();
                        })
                        .then(function(data) {
                            const list = Array.isArray(data) ? data : (data && typeof data === 'object' ? Object.values(data) : []);
                            self.contracts = list.map(function(c) {
                                if (!c.pi_numbers) c.pi_numbers = [];
                                return c;
                            });
                        })
                        .catch(function() { self.contracts = []; });
                },

                selectUnified(c) {
                    this.formContractNumber = c.contract_number || '';
                    this.formCustomerName = '';
                    this.unifiedDropdownOpen = false;
                },

                init() {
                    const self = this;
                    if (self.formSalesManager && self.contracts.length === 0) {
                        self.loadContracts();
                    }
                }
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .rotate-180 { transform: rotate(180deg); }
    </style>
</x-app-layout>
