<x-app-layout>
    <div x-data="deliveryDetailsSearch()" x-init="init()">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto order-lg-0">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Delivery Details Management</h1>
                    <p class="text-muted mb-0 small">View and manage delivery documents and uploads for all proforma invoices</p>
                </div>
                
            </div>
        </div>

        {{-- Search Section: searchable dropdowns (same design as over invoice) --}}
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
                <form method="GET" action="{{ route('proforma-invoices.delivery-details-index') }}" id="deliveryDetailsSearchForm">
                    <input type="hidden" name="sales_manager_id" x-model="formSalesManagerId">
                    <input type="hidden" name="pi_number" x-model="formPiNumber">
                    <input type="hidden" name="customer_name" x-model="formCustomerName">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                            <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                                <button type="button"
                                        @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; unifiedDropdownOpen = false"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                    <span x-text="formSalesManagerId ? salesManagers.find(m => m.id == formSalesManagerId)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
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
                                             :class="{ 'text-white': formSalesManagerId == m.id }"
                                             :style="formSalesManagerId == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
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
                                        @click="unifiedDropdownOpen = !unifiedDropdownOpen; salesManagerDropdownOpen = false; if (unifiedDropdownOpen && formSalesManagerId && proformaInvoices.length === 0) { loadProformaInvoices(); }"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!formSalesManagerId">
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
                                    <template x-if="filteredUnifiedPis.length === 0">
                                        <div class="p-3 text-center text-muted small">Select a sales manager first, or no rows match your search.</div>
                                    </template>
                                    <template x-for="pi in filteredUnifiedPis" :key="pi.id">
                                        <div class="d-flex align-items-center py-2 px-3"
                                             @click="selectUnified(pi)"
                                             style="cursor: pointer;"
                                             :class="{ 'text-white': String(formPiNumber || '') === String(pi.proforma_invoice_number || '') }"
                                             :style="String(formPiNumber || '') === String(pi.proforma_invoice_number || '') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                             onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                             onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-medium" x-text="pi.proforma_invoice_number"></div>
                                                <small class="d-block text-truncate" :class="String(formPiNumber || '') === String(pi.proforma_invoice_number || '') ? 'opacity-90' : 'text-muted'" x-text="unifiedRowSubtitle(pi)"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <small class="text-muted">Choose one PI; search matches contract and customer fields on that row.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('proforma-invoices.delivery-details-index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Results Section (same list header pattern as PI / PO list) --}}
        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-truck-loading text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Delivery Details List">Delivery Details List</h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $proformaInvoices->total() }} Total</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($proformaInvoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">S.No</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Number</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer Company</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sales Manager</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Total Amount</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Delivery Details Count</th>
                                    @if($canAssignMsUnloading ?? false)
                                    <th class="p-2 small fw-semibold d-none d-md-table-cell" style="color: var(--primary-color) !important;">MS Unloading Users</th>
                                    @endif
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proformaInvoices as $index => $pi)
                                    <tr class="border-bottom">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ ($proformaInvoices->currentPage() - 1) * $proformaInvoices->perPage() + $index + 1 }}</span></td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div style="color: #374151;">{{ $pi->buyer_company_name }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div style="color: #6b7280;">{{ $pi->contract->creator->name ?? ($pi->creator->name ?? 'N/A') }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div class="fw-semibold" style="color: var(--primary-color);">${{ number_format($pi->total_amount ?? 0, 2) }}</div>
                                        </td>
                                        <td class="px-2">
                                            @php
                                                $checkedCount = $pi->deliveryDetails ? $pi->deliveryDetails->where('is_received', true)->count() : 0;
                                                $totalCount = $pi->deliveryDetails ? $pi->deliveryDetails->count() : 0;
                                            @endphp
                                            @if($checkedCount > 0)
                                                <span class="badge bg-success">{{ $checkedCount }} Received</span>
                                                @if($totalCount > $checkedCount)
                                                    <span class="badge bg-secondary ms-1">{{ $totalCount - $checkedCount }} Pending</span>
                                                @endif
                                            @elseif($totalCount > 0)
                                                <span class="badge bg-warning">{{ $totalCount }} Pending</span>
                                            @else
                                                <span class="badge bg-secondary">No Details</span>
                                            @endif
                                        </td>
                                        @if($canAssignMsUnloading ?? false)
                                        <td class="px-2 d-none d-md-table-cell">
                                            <span class="small text-muted assigned-user-label-{{ $pi->id }}">
                                                {{ $pi->msUnloadingAssignedUsers->pluck('name')->join(', ') ?: '—' }}
                                            </span>
                                        </td>
                                        @endif
                                        <td class="px-2">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('proforma-invoices.delivery-details-view', $pi) }}" class="btn btn-sm btn-outline-info" title="View delivery details">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                                @can('edit proforma invoices')
                                                <a href="{{ route('proforma-invoices.delivery-details', $pi) }}" class="btn btn-sm {{ $pi->deliveryDetails && $pi->deliveryDetails->count() > 0 ? 'btn-info' : 'btn-success' }}" title="{{ $pi->deliveryDetails && $pi->deliveryDetails->count() > 0 ? 'Edit' : 'Add' }} Delivery Details">
                                                    <i class="fas fa-truck"></i>
                                                </a>
                                                @endcan
                                                @if($canAssignMsUnloading ?? false)
                                                @can('edit delivery detail')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Assign MS Unloading users"
                                                        @click="openAssignModal(@js($pi->id), @js($pi->proforma_invoice_number), @js($pi->msUnloadingAssignedUsers->pluck('id')->values()->all()))">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                                @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="text-center py-5 px-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                        <p class="text-muted mb-2">No Proforma Invoices found matching your search criteria.</p>
                        <p class="text-muted small">Try adjusting your filters or <a href="{{ route('proforma-invoices.index') }}">view all PIs</a> to add delivery details.</p>
                    </div>
                @endif
            </div>
            @if($proformaInvoices->count() > 0 && $proformaInvoices->hasPages())
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $proformaInvoices->firstItem() ?? 0 }} to {{ $proformaInvoices->lastItem() ?? 0 }} of {{ $proformaInvoices->total() }} PIs
                        </div>
                        <div>{{ $proformaInvoices->links() }}</div>
                    </div>
                </div>
            @elseif($proformaInvoices->count() > 0)
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="text-muted small text-center">
                        Showing {{ $proformaInvoices->total() }} PIs
                    </div>
                </div>
            @endif
        </div>

        @if($canAssignMsUnloading ?? false)
        @can('edit delivery detail')
        <div class="modal fade" id="msUnloadingAssignModal" tabindex="-1" aria-labelledby="msUnloadingAssignModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold" id="msUnloadingAssignModalLabel">Assign MS Unloading Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeAssignModal()"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <p class="text-muted small mb-3">PI: <span class="fw-medium text-dark" x-text="assignPiNumber"></span></p>
                        <label class="form-label fw-medium mb-2" style="color: #374151;">Users <small class="text-muted">(Multiple Select)</small></label>
                        <div class="position-relative" @click.away="assignUserDropdownOpen = false">
                            <button type="button"
                                    @click="assignUserDropdownOpen = !assignUserDropdownOpen"
                                    class="form-control text-start d-flex justify-content-between align-items-center"
                                    :disabled="assignLoading || assignSaving"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                <span class="text-truncate me-2" x-text="assignUsersButtonLabel"></span>
                                <i class="fas fa-chevron-down flex-shrink-0" :class="{ 'rotate-180': assignUserDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                            </button>
                            <div x-show="assignUserDropdownOpen"
                                 x-cloak
                                 class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                 style="z-index: 1060; max-height: 280px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                                 @click.stop>
                                <div class="p-2 border-bottom sticky-top bg-white" style="border-color: #e5e7eb !important; top: 0;">
                                    <input type="text"
                                           x-model="assignUserSearch"
                                           @click.stop
                                           placeholder="Search user name..."
                                           class="form-control form-control-sm"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <template x-if="assignLoading">
                                    <div class="p-3 text-center text-muted small">Loading users…</div>
                                </template>
                                <template x-if="!assignLoading && filteredAssignableUsers.length === 0">
                                    <div class="p-3 text-center text-muted small">No users match your search.</div>
                                </template>
                                <template x-for="u in filteredAssignableUsers" :key="u.id">
                                    <div class="d-flex align-items-center py-2 px-3"
                                         style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;"
                                         :style="isAssignUserSelected(u.id) ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : ''"
                                         @click="toggleAssignUser(u.id)">
                                        <input class="form-check-input me-3"
                                               type="checkbox"
                                               :checked="isAssignUserSelected(u.id)"
                                               style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                               @click.stop="toggleAssignUser(u.id)">
                                        <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0; font-size: 0.875rem; color: #374151;" x-text="u.name"></label>
                                        <i class="fas fa-check text-primary ms-2" x-show="isAssignUserSelected(u.id)" style="font-size: 0.875rem;"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <p x-show="assignError" x-text="assignError" class="text-danger small mt-2 mb-0"></p>
                        <p x-show="assignSuccess" x-text="assignSuccess" class="text-success small mt-2 mb-0"></p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" @click="closeAssignModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveAssign()" :disabled="assignSaving || assignLoading">
                            <span x-show="!assignSaving">Save assignment</span>
                            <span x-show="assignSaving"><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @endif
    </div>

    <script>
        function deliveryDetailsSearch() {
            return {
                canAssignMsUnloading: @json($canAssignMsUnloading ?? false),
                assignPiId: null,
                assignPiNumber: '',
                selectedAssignUserIds: [],
                assignableUsers: [],
                assignUserSearch: '',
                assignUserDropdownOpen: false,
                assignLoading: false,
                assignSaving: false,
                assignError: '',
                assignSuccess: '',
                assignModal: null,
                formSalesManagerId: @json((string) request('sales_manager_id', '')),
                formPiNumber: @json((string) request('pi_number', '')),
                formCustomerName: @json((string) request('customer_name', '')),
                salesManagerSearch: '',
                unifiedSearch: '',
                salesManagerDropdownOpen: false,
                unifiedDropdownOpen: false,
                salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
                proformaInvoices: [],

                unifiedRowSubtitle(pi) {
                    const parts = [];
                    if (pi.contract && pi.contract.contract_number) parts.push(pi.contract.contract_number);
                    if (pi.buyer_name) parts.push(pi.buyer_name);
                    if (pi.contract && pi.contract.company_name) parts.push(pi.contract.company_name);
                    if (pi.buyer_company_name) parts.push(pi.buyer_company_name);
                    return parts.join(' · ') || '—';
                },

                get unifiedSelectedLabel() {
                    const piKey = String(this.formPiNumber || '').trim();
                    if (piKey) {
                        const pi = this.proformaInvoices.find(p => String(p.proforma_invoice_number || '').trim() === piKey);
                        if (pi) {
                            return pi.proforma_invoice_number + ' — ' + this.unifiedRowSubtitle(pi);
                        }
                        return this.formPiNumber;
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

                get filteredUnifiedPis() {
                    if (!this.unifiedSearch || !String(this.unifiedSearch).trim()) {
                        return this.proformaInvoices;
                    }
                    const search = String(this.unifiedSearch).toLowerCase().trim();
                    return this.proformaInvoices.filter(pi => {
                        if (pi.proforma_invoice_number && String(pi.proforma_invoice_number).toLowerCase().includes(search)) return true;
                        if (pi.buyer_company_name && String(pi.buyer_company_name).toLowerCase().includes(search)) return true;
                        if (pi.buyer_name && String(pi.buyer_name).toLowerCase().includes(search)) return true;
                        const cn = pi.contract && pi.contract.contract_number;
                        const comp = pi.contract && pi.contract.company_name;
                        if (cn && String(cn).toLowerCase().includes(search)) return true;
                        if (comp && String(comp).toLowerCase().includes(search)) return true;
                        return false;
                    });
                },

                selectSalesManager(id) {
                    this.formSalesManagerId = id || '';
                    this.salesManagerDropdownOpen = false;
                    this.proformaInvoices = [];
                    this.unifiedSearch = '';
                    this.formPiNumber = '';
                    this.formCustomerName = '';
                    this.loadProformaInvoices();
                },
                loadProformaInvoices() {
                    if (!this.formSalesManagerId) {
                        this.proformaInvoices = [];
                        return;
                    }
                    const url = '{{ route('proforma-invoices.delivery-details.get-pis-by-sales-manager') }}?sales_manager_id=' + encodeURIComponent(this.formSalesManagerId);
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
                selectUnified(pi) {
                    this.formPiNumber = pi.proforma_invoice_number != null ? String(pi.proforma_invoice_number) : '';
                    this.formCustomerName = '';
                    this.unifiedDropdownOpen = false;
                },

                get filteredAssignableUsers() {
                    const list = Array.isArray(this.assignableUsers) ? this.assignableUsers : [];
                    const search = String(this.assignUserSearch || '').trim().toLowerCase();
                    if (!search) return list;
                    return list.filter(u => u.name && String(u.name).toLowerCase().includes(search));
                },

                get assignUsersButtonLabel() {
                    const n = this.selectedAssignUserIds.length;
                    if (n === 0) return 'Select users';
                    if (n === 1) {
                        const u = this.assignableUsers.find(x => String(x.id) === String(this.selectedAssignUserIds[0]));
                        return u ? u.name : '1 user selected';
                    }
                    return n + ' user(s) selected';
                },

                isAssignUserSelected(userId) {
                    return this.selectedAssignUserIds.map(String).includes(String(userId));
                },

                toggleAssignUser(userId) {
                    const id = String(userId);
                    const idx = this.selectedAssignUserIds.map(String).indexOf(id);
                    if (idx >= 0) {
                        this.selectedAssignUserIds.splice(idx, 1);
                    } else {
                        this.selectedAssignUserIds.push(parseInt(userId, 10));
                    }
                },

                init() {
                    if (this.formSalesManagerId && this.proformaInvoices.length === 0) {
                        this.loadProformaInvoices();
                    }
                    const el = document.getElementById('msUnloadingAssignModal');
                    if (el && typeof bootstrap !== 'undefined') {
                        this.assignModal = new bootstrap.Modal(el);
                    }
                },

                openAssignModal(piId, piNumber, currentUserIds) {
                    this.assignPiId = piId;
                    this.assignPiNumber = piNumber || '';
                    this.selectedAssignUserIds = Array.isArray(currentUserIds)
                        ? currentUserIds.map(id => parseInt(id, 10)).filter(id => !isNaN(id))
                        : [];
                    this.assignUserSearch = '';
                    this.assignUserDropdownOpen = false;
                    this.assignError = '';
                    this.assignSuccess = '';
                    this.assignLoading = true;
                    if (this.assignModal) {
                        this.assignModal.show();
                    }
                    if (this.assignableUsers.length > 0) {
                        this.assignLoading = false;
                        return;
                    }
                    fetch('{{ route('ms-unloading.assignable-users') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    })
                        .then(r => {
                            if (!r.ok) throw new Error('Could not load users.');
                            return r.json();
                        })
                        .then(users => {
                            this.assignableUsers = Array.isArray(users) ? users : [];
                        })
                        .catch(() => { this.assignError = 'Failed to load assignable users.'; })
                        .finally(() => { this.assignLoading = false; });
                },

                closeAssignModal() {
                    this.assignError = '';
                    this.assignSuccess = '';
                    this.assignUserDropdownOpen = false;
                    this.assignUserSearch = '';
                },

                saveAssign() {
                    if (!this.assignPiId) return;
                    this.assignSaving = true;
                    this.assignError = '';
                    this.assignSuccess = '';
                    const url = '{{ url('/proforma-invoices') }}/' + this.assignPiId + '/ms-unloading-assign';
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            user_ids: this.selectedAssignUserIds.map(id => parseInt(id, 10)),
                        }),
                    })
                        .then(async r => {
                            const data = await r.json().catch(() => ({}));
                            if (!r.ok) throw new Error(data.message || 'Assignment failed.');
                            return data;
                        })
                        .then(data => {
                            this.assignSuccess = data.message || 'Saved.';
                            const label = document.querySelector('.assigned-user-label-' + this.assignPiId);
                            if (label) {
                                label.textContent = data.assigned_label || '—';
                            }
                            setTimeout(() => {
                                if (this.assignModal) this.assignModal.hide();
                                this.assignSuccess = '';
                                this.assignUserDropdownOpen = false;
                            }, 800);
                        })
                        .catch(err => { this.assignError = err.message || 'Assignment failed.'; })
                        .finally(() => { this.assignSaving = false; });
                },
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .rotate-180 { transform: rotate(180deg); }
    </style>
</x-app-layout>
