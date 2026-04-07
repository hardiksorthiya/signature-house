<x-app-layout>
    <div x-data="spareListSearch()" x-init="init()">
        <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center flex-wrap gap-2" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list-alt text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Spare List</h1>
                            <p class="text-muted mb-0 small">Spare list per PI (Delivery Documents & Quantity)</p>
                            <span class="badge mt-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $proformaInvoices->total() }} Total</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-2 mt-lg-0">
                        <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-primary d-flex align-items-center" style="border-radius: 8px;"><i class="fas fa-list me-2"></i>View All PIs</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;"><i class="fas fa-search text-white"></i></div>
                    <div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Search</h2>
                        <p class="text-muted small mb-0">Select sales manager, PI number or contract number to filter</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('ms-unloading-spare-list.index') }}" id="spareListSearchForm">
                    <input type="hidden" name="sales_manager_id" x-model="formSalesManagerId">
                    <input type="hidden" name="pi_number" x-model="formPiNumber">
                    <input type="hidden" name="contract_number" x-model="formContractNumber">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                            <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                                <button type="button" @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; piDropdownOpen = false; contractDropdownOpen = false" class="form-control text-start d-flex justify-content-between align-items-center" style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                    <span x-text="formSalesManagerId ? salesManagers.find(m => m.id == formSalesManagerId)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': salesManagerDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                                </button>
                                <div x-show="salesManagerDropdownOpen" x-cloak class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;" @click.stop>
                                    <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;"><input type="text" x-model="salesManagerSearch" @click.stop placeholder="Search sales manager..." class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;"></div>
                                    <template x-if="filteredSalesManagers.length === 0"><div class="p-3 text-center text-muted small">No sales managers found</div></template>
                                    <template x-for="m in filteredSalesManagers" :key="m.id"><div class="d-flex align-items-center py-2 px-3" @click="selectSalesManager(m.id)" style="cursor: pointer;" :class="{ 'text-white': formSalesManagerId == m.id }" :style="formSalesManagerId == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''" onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'" onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''"><div class="flex-grow-1"><div class="fw-medium" x-text="m.name"></div></div></div></template>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="color: #374151;">PI Number</label>
                            <div class="position-relative" @click.away="piDropdownOpen = false">
                                <button type="button" @click="piDropdownOpen = !piDropdownOpen; salesManagerDropdownOpen = false; contractDropdownOpen = false" class="form-control text-start d-flex justify-content-between align-items-center" style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;" :disabled="proformaInvoices.length === 0">
                                    <span x-text="formPiNumber || 'Select PI Number'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': piDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                                </button>
                                <div x-show="piDropdownOpen" x-cloak class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;" @click.stop>
                                    <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;"><input type="text" x-model="piSearch" @click.stop placeholder="Search proforma invoice..." class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;"></div>
                                    <template x-if="filteredProformaInvoices.length === 0"><div class="p-3 text-center text-muted small">Select a sales manager first or no PIs found.</div></template>
                                    <template x-for="pi in filteredProformaInvoices" :key="pi.id"><div class="d-flex align-items-center py-2 px-3" @click="selectPi(pi)" style="cursor: pointer;" :class="{ 'text-white': formPiNumber == pi.proforma_invoice_number }" :style="formPiNumber == pi.proforma_invoice_number ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''" onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'" onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''"><div class="flex-grow-1"><div class="fw-medium" x-text="pi.proforma_invoice_number"></div><small class="d-block" x-text="pi.buyer_company_name || ''"></small></div></div></template>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" style="color: #374151;">Contract Number</label>
                            <div class="position-relative" @click.away="contractDropdownOpen = false">
                                <button type="button" @click="contractDropdownOpen = !contractDropdownOpen; salesManagerDropdownOpen = false; piDropdownOpen = false" class="form-control text-start d-flex justify-content-between align-items-center" style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;" :disabled="contracts.length === 0">
                                    <span x-text="formContractNumber || 'Select Contract Number'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': contractDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                                </button>
                                <div x-show="contractDropdownOpen" x-cloak class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;" @click.stop>
                                    <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;"><input type="text" x-model="contractSearch" @click.stop placeholder="Search contract number..." class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;"></div>
                                    <template x-if="filteredContracts.length === 0"><div class="p-3 text-center text-muted small">Select a sales manager first or no contracts found.</div></template>
                                    <template x-for="c in filteredContracts" :key="c.id"><div class="d-flex align-items-center py-2 px-3" @click="selectContract(c)" style="cursor: pointer;" :class="{ 'text-white': formContractNumber == c.contract_number }" :style="formContractNumber == c.contract_number ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''" onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'" onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''"><div class="flex-grow-1"><div class="fw-medium" x-text="c.contract_number"></div><small class="d-block" x-text="(c.buyer_name || '') + (c.company_name ? ' (' + c.company_name + ')' : '')"></small></div></div></template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;"><i class="fas fa-search me-2"></i>Search</button>
                        <a href="{{ route('ms-unloading-spare-list.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;"><i class="fas fa-redo me-2"></i>Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list-alt text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Spare List per PI">Spare List per PI</h2>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($proformaInvoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">PI Number</th>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Contract Number</th>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Sales Manager</th>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Spare List</th>
                                    <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proformaInvoices as $index => $pi)
                                <tr class="border-bottom">
                                    <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ ($proformaInvoices->currentPage() - 1) * $proformaInvoices->perPage() + $index + 1 }}</span></td>
                                    <td class="px-2"><div class="fw-medium" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</div></td>
                                    <td class="px-2"><div style="color: #6b7280;">{{ $pi->contract->contract_number ?? 'N/A' }}</div></td>
                                    <td class="px-2"><div style="color: #6b7280;">{{ $pi->contract->creator->name ?? ($pi->creator->name ?? 'N/A') }}</div></td>
                                    <td class="px-2">
                                        @php
                                            $spareCount = $pi->piSpareLists ? $pi->piSpareLists->count() : 0;
                                            $fulfilledCount = $pi->piSpareLists ? $pi->piSpareLists->where('is_fulfilled', true)->count() : 0;
                                        @endphp
                                        @if($spareCount > 0)
                                            <span class="badge bg-success" title="{{ $fulfilledCount }} fulfilled">{{ $fulfilledCount }} / {{ $spareCount }} fulfilled</span>
                                        @else
                                            <span class="badge bg-secondary">No List</span>
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        <div class="d-flex gap-2">
                                            @canany(['create spare list', 'edit spare list'])
                                            <a href="{{ route('ms-unloading-spare-list.show', $pi) }}" class="btn btn-sm {{ $pi->piSpareLists && $pi->piSpareLists->count() > 0 ? 'btn-info' : 'btn-success' }}" title="{{ $pi->piSpareLists && $pi->piSpareLists->count() > 0 ? 'Edit' : 'Add' }} Spare List"><i class="fas fa-list-alt"></i></a>
                                            @endcanany
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                        <p class="text-muted mb-2">No Proforma Invoices found matching your search criteria.</p>
                        <p class="text-muted small">Try adjusting your filters or <a href="{{ route('proforma-invoices.index') }}">view all PIs</a> to add spare list.</p>
                    </div>
                @endif
            </div>
            @if($proformaInvoices->count() > 0 && $proformaInvoices->hasPages())
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $proformaInvoices->firstItem() }} to {{ $proformaInvoices->lastItem() }} of {{ $proformaInvoices->total() }} proforma invoices
                        </div>
                        <div>{{ $proformaInvoices->links() }}</div>
                    </div>
                </div>
            @elseif($proformaInvoices->count() > 0)
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="text-muted small text-center">
                        Showing {{ $proformaInvoices->total() }} proforma invoices
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function spareListSearch() {
            return {
                formSalesManagerId: '{{ request('sales_manager_id') ?? '' }}',
                formPiNumber: '{{ request('pi_number') ?? '' }}',
                formContractNumber: '{{ request('contract_number') ?? '' }}',
                salesManagerSearch: '', piSearch: '', contractSearch: '',
                salesManagerDropdownOpen: false, piDropdownOpen: false, contractDropdownOpen: false,
                salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
                proformaInvoices: [], contracts: [],
                get filteredSalesManagers() { if (!this.salesManagerSearch) return this.salesManagers; const s = this.salesManagerSearch.toLowerCase(); return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(s))); },
                get filteredProformaInvoices() { if (!this.piSearch) return this.proformaInvoices; const s = this.piSearch.toLowerCase(); return this.proformaInvoices.filter(pi => (pi.proforma_invoice_number && pi.proforma_invoice_number.toLowerCase().includes(s)) || (pi.buyer_company_name && pi.buyer_company_name.toLowerCase().includes(s))); },
                get filteredContracts() { if (!this.contractSearch) return this.contracts; const s = this.contractSearch.toLowerCase(); return this.contracts.filter(c => (c.contract_number && c.contract_number.toLowerCase().includes(s)) || (c.buyer_name && c.buyer_name.toLowerCase().includes(s)) || (c.company_name && c.company_name.toLowerCase().includes(s))); },
                selectSalesManager(id) { this.formSalesManagerId = id || ''; this.salesManagerDropdownOpen = false; this.loadData(); },
                loadData() {
                    if (!this.formSalesManagerId) { this.proformaInvoices = []; this.contracts = []; return; }
                    fetch('{{ route('ms-unloading-spare-list.get-pis') }}?sales_manager_id=' + encodeURIComponent(this.formSalesManagerId)).then(r => r.ok ? r.json() : Promise.reject()).then(data => { this.proformaInvoices = Array.isArray(data) ? data : []; }).catch(() => { this.proformaInvoices = []; });
                    fetch('{{ route('ms-unloading-spare-list.get-contracts') }}?sales_manager_id=' + encodeURIComponent(this.formSalesManagerId)).then(r => r.ok ? r.json() : Promise.reject()).then(data => { this.contracts = Array.isArray(data) ? data : []; }).catch(() => { this.contracts = []; });
                },
                selectPi(pi) { this.formPiNumber = pi.proforma_invoice_number || ''; this.formContractNumber = ''; this.piDropdownOpen = false; },
                selectContract(c) { this.formContractNumber = c.contract_number || ''; this.contractDropdownOpen = false; },
                init() { if (this.formSalesManagerId && this.proformaInvoices.length === 0) this.loadData(); }
            };
        }
    </script>
    <style>[x-cloak]{display:none !important;}.rotate-180{transform:rotate(180deg);}</style>
</x-app-layout>
