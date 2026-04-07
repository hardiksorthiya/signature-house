<x-app-layout>
    <div x-data="iaFittingSearch()" x-init="init()">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">IA Fitting</h1>
                    <p class="text-muted mb-0 small">View and manage IA fitting details for all proforma invoices.</p>
                </div>
                <div class="col-12 col-lg-auto ms-lg-auto">
                    <a href="{{ route('proforma-invoices.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-list me-2"></i>View All PIs
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 8px;">
                <p class="mb-0 small">{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Search</h2>
                        <p class="text-muted small mb-0">Pick a PI or contract from the list, or type in the box and use Search — includes sales manager, company, customer.</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="position-relative" @click.away="searchSectionDropdownOpen = false">
                    <label class="form-label fw-medium" style="color: #374151;">PI / Contract / sales manager</label>
                    <button type="button"
                            @click="searchSectionDropdownOpen = !searchSectionDropdownOpen; if (searchSectionDropdownOpen) { loadData(); }"
                            class="form-control text-start d-flex justify-content-between align-items-center"
                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                        <span class="text-truncate me-2" style="min-width: 0;" x-text="unifiedSelectedLabel"></span>
                        <i class="fas fa-chevron-down flex-shrink-0" :class="{ 'rotate-180': searchSectionDropdownOpen }" style="transition: transform 0.2s ease;"></i>
                    </button>
                    <div x-show="searchSectionDropdownOpen"
                         x-cloak
                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                         style="z-index: 1000; max-height: 360px; overflow: hidden; display: flex; flex-direction: column; border-color: #e5e7eb !important; border-radius: 8px;"
                         @click.stop>
                        <div class="p-2 border-bottom flex-shrink-0" style="border-color: #e5e7eb !important;">
                            <input type="text"
                                   x-model="unifiedSearch"
                                   @input.debounce.400ms="loadData()"
                                   @click.stop
                                   placeholder="PI #, contract #, company, customer, sales manager…"
                                   class="form-control form-control-sm"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        </div>
                        <div class="overflow-y-auto flex-grow-1" style="max-height: 280px;">
                            <template x-if="loading">
                                <div class="p-3 text-center text-muted small">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading…
                                </div>
                            </template>
                            <template x-if="!loading && unifiedRows.length === 0">
                                <div class="p-3 text-center text-muted small">No rows match. Try different keywords.</div>
                            </template>
                            <template x-for="row in unifiedRows" :key="'main-' + row.kind + '-' + row.id">
                                <div class="d-flex align-items-start py-2 px-3 border-bottom"
                                     style="cursor: pointer; border-color: #f3f4f6 !important;"
                                     @click="selectUnified(row)"
                                     :class="{ 'text-white': rowSelected(row) }"
                                     :style="rowSelected(row) ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                     onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f9fafb'"
                                     onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                    <div class="me-2 flex-shrink-0">
                                        <span class="badge rounded-pill"
                                              :class="row.kind === 'contract' ? 'bg-secondary' : 'bg-primary'"
                                              style="font-size: 0.65rem;"
                                              x-text="row.kind === 'contract' ? 'Contract' : 'PI'"></span>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-medium text-truncate" x-text="unifiedPrimaryLabel(row)"></div>
                                        <small class="d-block text-truncate" :class="rowSelected(row) ? 'opacity-90' : 'text-muted'" x-text="unifiedRowSubtitle(row)"></small>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" @click="submitForm()" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="{{ route('ia-fitting.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center justify-content-between flex-grow-1" style="min-width: 0;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-wrench text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="IA fitting list">IA fitting list</h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $proformaInvoices->total() }} Total</span>
                        </div>
                        @if (request()->filled('pi_number') || request()->filled('contract_number') || request()->filled('search'))
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <a href="{{ route('ia-fitting.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear search"><i class="fas fa-times"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                            <tr>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Number</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Company</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Customer</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Number</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sales manager</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">IA fitting details</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proformaInvoices as $pi)
                                @php
                                    $contract = $pi->contract;
                                    $customerName = $contract?->buyer_name ?? $pi->buyer_company_name ?? 'N/A';
                                    $companyName = $contract?->company_name ?? $pi->buyer_company_name ?? 'N/A';
                                    $salesManager = $contract?->creator?->name ?? $pi->creator?->name ?? 'N/A';
                                    $detailCount = $pi->iaFittingDetails ? $pi->iaFittingDetails->count() : 0;
                                @endphp
                                <tr class="border-bottom">
                                    <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $proformaInvoices->firstItem() + $loop->index }}</span></td>
                                    <td class="px-2"><div class="fw-medium" style="color: #1f2937;">{{ $pi->proforma_invoice_number }}</div></td>
                                    <td class="px-2"><div class="fw-medium" style="color: #1f2937;">{{ $companyName }}</div></td>
                                    <td class="px-2"><div style="color: #6b7280;">{{ $customerName }}</div></td>
                                    <td class="px-2"><div style="color: #6b7280;">{{ $contract?->contract_number ?? 'N/A' }}</div></td>
                                    <td class="px-2"><div style="color: #6b7280;">{{ $salesManager }}</div></td>
                                    <td class="px-2">
                                        @if ($detailCount > 0)
                                            <span class="badge bg-success">{{ $detailCount }} details</span>
                                        @else
                                            <span class="badge bg-secondary">No details</span>
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        <div class="d-flex gap-2 flex-wrap" role="group">
                                            @can('view ia fitting')
                                                <a href="{{ route('ia-fitting.view', $pi) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="View IA fitting details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @canany(['create ia fitting', 'edit ia fitting'])
                                                <a href="{{ route('ia-fitting.show', $pi) }}"
                                                   class="btn btn-sm {{ $detailCount > 0 ? 'btn-info' : 'btn-success' }}"
                                                   title="{{ $detailCount > 0 ? 'Edit' : 'Add' }} IA fitting details">
                                                    <i class="fas fa-wrench"></i>
                                                </a>
                                            @endcanany
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-wrench fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                            <p class="mb-0">No proforma invoices found.</p>
                                            <span class="text-muted mt-1 d-block">Use the Search section above or clear filters to see all records</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($proformaInvoices->hasPages())
                <div class="card-footer border-0 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $proformaInvoices->firstItem() ?? 0 }} to {{ $proformaInvoices->lastItem() ?? 0 }} of {{ $proformaInvoices->total() }} proforma invoices
                        </div>
                        <div>
                            {{ $proformaInvoices->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card-footer border-0 bg-transparent">
                    <div class="text-muted text-center small">
                        Showing {{ $proformaInvoices->count() }} of {{ $proformaInvoices->total() }} proforma invoices
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function iaFittingSearch() {
            return {
                searchSectionDropdownOpen: false,
                formPiNumber: @json((string) request('pi_number', '')),
                formContractNumber: @json((string) request('contract_number', '')),
                unifiedSearch: @json((string) request('search', '')),
                loading: false,
                apiPis: [],
                apiContracts: [],

                get unifiedRows() {
                    const rows = [
                        ...(this.apiContracts || []).map(r => ({ ...r, kind: 'contract' })),
                        ...(this.apiPis || []).map(r => ({ ...r, kind: 'pi' })),
                    ];
                    return rows.sort((a, b) => {
                        const ak = a.kind === 'contract' ? String(a.contract_number || '') : String(a.proforma_invoice_number || '');
                        const bk = b.kind === 'contract' ? String(b.contract_number || '') : String(b.proforma_invoice_number || '');
                        return ak.localeCompare(bk, undefined, { numeric: true, sensitivity: 'base' });
                    });
                },

                unifiedPrimaryLabel(row) {
                    if (!row) return '';
                    return row.kind === 'contract' ? String(row.contract_number || '') : String(row.proforma_invoice_number || '');
                },

                unifiedRowSubtitle(row) {
                    if (!row) return '—';
                    const sm = row.sales_manager_name ? ` · ${row.sales_manager_name}` : '';
                    if (row.kind === 'contract') {
                        const parts = [row.company_name, row.buyer_name].filter(Boolean);
                        return (parts.join(' · ') || 'Contract') + sm;
                    }
                    const parts = [];
                    if (row.contract_number) parts.push(row.contract_number);
                    if (row.company_name) parts.push(row.company_name);
                    if (row.buyer_name) parts.push(row.buyer_name);
                    if (row.buyer_company_name && !parts.includes(row.buyer_company_name)) parts.push(row.buyer_company_name);
                    return (parts.join(' · ') || '—') + sm;
                },

                rowSelected(row) {
                    if (!row) return false;
                    if (row.kind === 'pi') {
                        return String(this.formPiNumber || '') === String(row.proforma_invoice_number || '');
                    }
                    return String(this.formContractNumber || '') === String(row.contract_number || '');
                },

                get unifiedSelectedLabel() {
                    const pi = String(this.formPiNumber || '').trim();
                    const ctr = String(this.formContractNumber || '').trim();
                    if (pi) {
                        const row = (this.apiPis || []).find(p => String(p.proforma_invoice_number || '') === pi);
                        return row ? (pi + ' — ' + this.unifiedRowSubtitle({ ...row, kind: 'pi' })) : pi;
                    }
                    if (ctr) {
                        const row = (this.apiContracts || []).find(c => String(c.contract_number || '') === ctr);
                        return row ? (ctr + ' — ' + this.unifiedRowSubtitle({ ...row, kind: 'contract' })) : ctr;
                    }
                    const t = String(this.unifiedSearch || '').trim();
                    if (t) return 'Text search: ' + t;
                    return 'Search Here...';
                },

                selectUnified(row) {
                    if (!row) return;
                    if (row.kind === 'pi') {
                        this.formPiNumber = row.proforma_invoice_number != null ? String(row.proforma_invoice_number) : '';
                        this.formContractNumber = '';
                    } else {
                        this.formContractNumber = row.contract_number != null ? String(row.contract_number) : '';
                        this.formPiNumber = '';
                    }
                    this.searchSectionDropdownOpen = false;
                },

                loadData() {
                    this.loading = true;
                    const q = encodeURIComponent(this.unifiedSearch || '');
                    fetch(`{{ route('ia-fitting.unified-items') }}?q=${q}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.ok ? r.json() : Promise.reject())
                        .then(data => {
                            this.apiPis = data.proforma_invoices || [];
                            this.apiContracts = data.contracts || [];
                        })
                        .catch(() => {
                            this.apiPis = [];
                            this.apiContracts = [];
                        })
                        .finally(() => { this.loading = false; });
                },

                submitForm() {
                    const base = @json(route('ia-fitting.index'));
                    const params = new URLSearchParams();
                    const p = String(this.formPiNumber || '').trim();
                    const c = String(this.formContractNumber || '').trim();
                    const s = String(this.unifiedSearch || '').trim();
                    if (p) {
                        params.set('pi_number', p);
                    } else if (c) {
                        params.set('contract_number', c);
                    } else if (s) {
                        params.set('search', s);
                    }
                    const qs = params.toString();
                    window.location.href = qs ? `${base}?${qs}` : base;
                },

                init() {
                    if (String(this.formPiNumber || '').trim() || String(this.formContractNumber || '').trim() || String(this.unifiedSearch || '').trim()) {
                        this.loadData();
                    }
                }
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .rotate-180 { transform: rotate(180deg); }
        .list-card { min-width: 0; }
        .list-header { flex-wrap: wrap; }
        .list-header-title-row { min-width: 0; }
    </style>
</x-app-layout>
