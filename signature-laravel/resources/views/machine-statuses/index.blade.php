<x-app-layout>
    <div x-data="machineStatusSearch()" x-init="init()">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto order-lg-0">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Machine Status Management</h1>
                    <p class="text-muted mb-0 small">Track workflow status for contracts and proforma invoices</p>
                </div>
                
            </div>
        </div>

        {{-- Search Section: searchable dropdowns (same design as delivery detail) --}}
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
                <form method="GET" action="{{ route('machine-statuses.index') }}" id="machineStatusSearchForm">
                    <input type="hidden" name="sales_manager_id" x-model="formSalesManagerId">
                    <input type="hidden" name="pi_number" x-model="formPiNumber">
                    <input type="hidden" name="contract_number" x-model="formContractNumber">
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
                            <label class="form-label fw-medium" style="color: #374151;">Contract / PI / buyer / company</label>
                            <div class="position-relative" @click.away="unifiedDropdownOpen = false">
                                <button type="button"
                                        @click="unifiedDropdownOpen = !unifiedDropdownOpen; salesManagerDropdownOpen = false; if (unifiedDropdownOpen && formSalesManagerId && unifiedItems.length === 0) { loadData(); }"
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
                                    <template x-if="filteredUnifiedItems.length === 0">
                                        <div class="p-3 text-center text-muted small">Select a sales manager first, or no rows match your search.</div>
                                    </template>
                                    <template x-for="row in filteredUnifiedItems" :key="row.kind + '-' + row.id">
                                        <div class="d-flex align-items-center py-2 px-3"
                                             @click="selectUnified(row)"
                                             style="cursor: pointer;"
                                             :class="{ 'text-white': rowSelected(row) }"
                                             :style="rowSelected(row) ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                             onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                             onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-medium" x-text="unifiedPrimaryLabel(row)"></div>
                                                <small class="d-block text-truncate" :class="rowSelected(row) ? 'opacity-90' : 'text-muted'" x-text="unifiedRowSubtitle(row)"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <small class="text-muted">Contract rows filter by contract only; PI rows filter by that proforma invoice.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('machine-statuses.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Results Section --}}
        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-tasks text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Status List">Status List</h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $paginator->total() }} Total</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($paginator->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract No.</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Number</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer company</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Customer name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paginator as $index => $item)
                                @php
                                    $contract = $item['contract'];
                                    $pi = $item['proforma_invoice'];
                                    $status = $item['machine_status'];
                                    $contractNumber = $contract ? $contract->contract_number : ($pi && $pi->contract ? $pi->contract->contract_number : '—');
                                    if ($pi) {
                                        $piNumber = $pi->proforma_invoice_number ?: '—';
                                    } elseif ($contract && $contract->relationLoaded('proformaInvoices') && $contract->proformaInvoices->isNotEmpty()) {
                                        $piNumber = $contract->proformaInvoices->pluck('proforma_invoice_number')->filter()->unique()->values()->implode(', ');
                                    } else {
                                        $piNumber = '—';
                                    }
                                    $buyerCompany = '—';
                                    $customerName = '—';
                                    if ($contract) {
                                        $buyerCompany = $contract->company_name ?: '—';
                                        $customerName = $contract->buyer_name ?: '—';
                                    } elseif ($pi) {
                                        $linkedContract = $pi->contract;
                                        $buyerCompany = $pi->buyer_company_name
                                            ?: ($linkedContract ? ($linkedContract->company_name ?: '—') : '—');
                                        $customerName = $linkedContract ? ($linkedContract->buyer_name ?: '—') : '—';
                                    }
                                    $statusStepLabels = ['Contract date', 'Proforma invoice', 'China payment', 'Actual dispatch', 'Expected arrival', 'Actual arrival'];
                                    $lastStatusText = 'No status';
                                    $lastStatusBadge = 'secondary';
                                    if ($status) {
                                        $flags = [
                                            (bool) $status->contract_date_completed,
                                            (bool) $status->proforma_invoice_completed,
                                            (bool) $status->china_payment_completed,
                                            (bool) $status->actual_dispatch_completed,
                                            (bool) $status->expected_arrival_completed,
                                            (bool) $status->actual_arrival_completed,
                                        ];
                                        $lastCompletedIdx = -1;
                                        for ($si = 5; $si >= 0; $si--) {
                                            if ($flags[$si]) {
                                                $lastCompletedIdx = $si;
                                                break;
                                            }
                                        }
                                        if ($lastCompletedIdx === -1) {
                                            $lastStatusText = 'Not started · next: ' . $statusStepLabels[0];
                                            $lastStatusBadge = 'warning';
                                        } elseif ($lastCompletedIdx === 5) {
                                            $lastStatusText = 'All complete · ' . $statusStepLabels[5];
                                            $lastStatusBadge = 'success';
                                        } else {
                                            $lastStatusText = 'Last: ' . $statusStepLabels[$lastCompletedIdx] . ' · next: ' . $statusStepLabels[$lastCompletedIdx + 1];
                                            $lastStatusBadge = 'info';
                                        }
                                    }
                                @endphp
                                <tr class="border-bottom">
                                    <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $paginator->firstItem() + $loop->index }}</span></td>
                                    <td class="px-2">
                                        <div class="fw-medium" style="color: #1f2937;">{{ $contractNumber }}</div>
                                    </td>
                                    <td class="px-2">
                                        <div class="fw-medium" style="color: #1f2937;">{{ $piNumber }}</div>
                                    </td>
                                    <td class="px-2">
                                        <div style="color: #374151;">{{ $buyerCompany }}</div>
                                    </td>
                                    <td class="px-2">
                                        <div style="color: #374151;">{{ $customerName }}</div>
                                    </td>
                                    
                                    <td class="px-2">
                                        @if($item['type'] === 'contract')
                                            <a href="{{ route('machine-statuses.create', ['contract_id' => $contract->id, 'view' => 1]) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="View status">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('machine-statuses.create', ['proforma_invoice_id' => $pi->id, 'view' => 1]) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="View status">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                        <p class="text-muted mb-2">No Contracts or Proforma Invoices found matching your search criteria.</p>
                        <p class="text-muted small">Try adjusting your filters or <a href="{{ route('contracts.index') }}">view all contracts</a> to add machine status.</p>
                    </div>
                @endif
            </div>
            @if($paginator->count() > 0 && $paginator->hasPages())
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} items
                        </div>
                        <div>
                            {{ $paginator->links() }}
                        </div>
                    </div>
                </div>
            @elseif($paginator->count() > 0)
                <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="text-muted small text-center">
                        Showing {{ $paginator->total() }} items
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function machineStatusSearch() {
            return {
                formSalesManagerId: @json((string) request('sales_manager_id', '')),
                formPiNumber: @json(request('pi_number', '')),
                formContractNumber: @json(request('contract_number', '')),
                salesManagerSearch: '',
                unifiedSearch: '',
                salesManagerDropdownOpen: false,
                unifiedDropdownOpen: false,
                salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
                proformaInvoices: [],
                contracts: [],

                unifiedPrimaryLabel(row) {
                    if (!row) return '';
                    if (row.kind === 'contract') return row.contract_number || '';
                    return row.proforma_invoice_number || '';
                },

                unifiedRowSubtitle(row) {
                    if (!row) return '—';
                    if (row.kind === 'contract') {
                        const parts = [];
                        if (row.buyer_name) parts.push(row.buyer_name);
                        if (row.company_name) parts.push(row.company_name);
                        return parts.join(' · ') || 'Contract';
                    }
                    const parts = [];
                    if (row.contract_number) parts.push(row.contract_number);
                    if (row.buyer_name) parts.push(row.buyer_name);
                    if (row.company_name) parts.push(row.company_name);
                    if (row.buyer_company_name) parts.push(row.buyer_company_name);
                    return parts.join(' · ') || '—';
                },

                rowSelected(row) {
                    if (!row) return false;
                    if (row.kind === 'pi' || row.proforma_invoice_number) {
                        return String(this.formPiNumber || '') === String(row.proforma_invoice_number || '');
                    }
                    if (row.kind === 'contract' || row.contract_number) {
                        return String(this.formContractNumber || '') === String(row.contract_number || '');
                    }
                    return false;
                },

                get unifiedItems() {
                    const pis = (this.proformaInvoices || []).filter(r => r && (r.kind === 'pi' || r.proforma_invoice_number));
                    const ctr = (this.contracts || []).filter(r => r && (r.kind === 'contract' || (r.contract_number && !r.proforma_invoice_number)));
                    const all = [...ctr, ...pis];
                    return all.sort((a, b) => {
                        const ac = (a.contract_number || a.proforma_invoice_number || '').toString();
                        const bc = (b.contract_number || b.proforma_invoice_number || '').toString();
                        const cmp = ac.localeCompare(bc, undefined, { numeric: true, sensitivity: 'base' });
                        if (cmp !== 0) return cmp;
                        if (a.kind === b.kind) return 0;
                        return a.kind === 'contract' ? -1 : 1;
                    });
                },

                get unifiedSelectedLabel() {
                    const piKey = String(this.formPiNumber || '').trim();
                    const ctrKey = String(this.formContractNumber || '').trim();
                    if (piKey) {
                        const row = (this.proformaInvoices || []).find(p => String(p.proforma_invoice_number || '').trim() === piKey);
                        if (row) return row.proforma_invoice_number + ' — ' + this.unifiedRowSubtitle(row);
                        return this.formPiNumber;
                    }
                    if (ctrKey) {
                        const row = (this.contracts || []).find(c => String(c.contract_number || '').trim() === ctrKey);
                        if (row) return row.contract_number + ' — ' + this.unifiedRowSubtitle(row);
                        return this.formContractNumber;
                    }
                    return 'Search contract number, buyer name, company name, PI number…';
                },

                get filteredSalesManagers() {
                    if (!this.salesManagerSearch) return this.salesManagers;
                    const search = this.salesManagerSearch.toLowerCase();
                    return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(search)));
                },

                get filteredUnifiedItems() {
                    const items = this.unifiedItems;
                    if (!this.unifiedSearch || !String(this.unifiedSearch).trim()) return items;
                    const s = String(this.unifiedSearch).toLowerCase().trim();
                    return items.filter(row => {
                        const fields = [
                            row.contract_number,
                            row.proforma_invoice_number,
                            row.buyer_name,
                            row.company_name,
                            row.buyer_company_name,
                        ];
                        return fields.some(f => f && String(f).toLowerCase().includes(s));
                    });
                },

                selectSalesManager(id) {
                    this.formSalesManagerId = id || '';
                    this.salesManagerDropdownOpen = false;
                    this.proformaInvoices = [];
                    this.contracts = [];
                    this.unifiedSearch = '';
                    this.formPiNumber = '';
                    this.formContractNumber = '';
                    this.loadData();
                },
                loadData() {
                    if (!this.formSalesManagerId) {
                        this.proformaInvoices = [];
                        this.contracts = [];
                        return;
                    }
                    const smId = encodeURIComponent(this.formSalesManagerId);
                    Promise.all([
                        fetch(`{{ route('machine-statuses.get-pis') }}?sales_manager_id=${smId}`).then(r => r.ok ? r.json() : []),
                        fetch(`{{ route('machine-statuses.get-contracts') }}?sales_manager_id=${smId}`).then(r => r.ok ? r.json() : [])
                    ]).then(([pis, contracts]) => {
                        this.proformaInvoices = Array.isArray(pis)
                            ? pis.map(p => (p && typeof p === 'object' ? { ...p, kind: 'pi' } : p)).filter(Boolean)
                            : [];
                        this.contracts = Array.isArray(contracts)
                            ? contracts.map(c => (c && typeof c === 'object' ? { ...c, kind: 'contract' } : c)).filter(Boolean)
                            : [];
                    }).catch(() => {
                        this.proformaInvoices = [];
                        this.contracts = [];
                    });
                },
                selectUnified(row) {
                    if (!row) return;
                    if (row.kind === 'pi' || row.proforma_invoice_number) {
                        this.formPiNumber = row.proforma_invoice_number != null ? String(row.proforma_invoice_number) : '';
                        this.formContractNumber = '';
                    } else {
                        this.formContractNumber = row.contract_number != null ? String(row.contract_number) : '';
                        this.formPiNumber = '';
                    }
                    this.unifiedDropdownOpen = false;
                },

                init() {
                    if (this.formSalesManagerId && this.proformaInvoices.length === 0 && this.contracts.length === 0) {
                        this.loadData();
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
