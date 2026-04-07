@php
    $paymentSearchSelectionLabel = '';
    if (isset($contract)) {
        $paymentSearchSelectionLabel = $contract->contract_number;
        $bits = array_filter([$contract->buyer_name, $contract->company_name]);
        if ($bits) {
            $paymentSearchSelectionLabel .= ' — ' . implode(' · ', $bits);
        }
    } elseif (isset($proformaInvoice)) {
        $paymentSearchSelectionLabel = $proformaInvoice->proforma_invoice_number;
        $bits = array_filter([
            $proformaInvoice->buyer_company_name,
            $proformaInvoice->contract?->contract_number,
        ]);
        if ($bits) {
            $paymentSearchSelectionLabel .= ' — ' . implode(' · ', $bits);
        }
    }
@endphp
<x-app-layout>
    <div x-data="{
        redirectBase: @js(route('payments.return-payment')),
        selectedSalesManager: @js((string) request('sales_manager', '')),
        lockedSelectionLabel: @js($paymentSearchSelectionLabel),
        currentContractId: @js(isset($contract) ? $contract->id : null),
        currentPiId: @js(isset($proformaInvoice) ? $proformaInvoice->id : null),
        salesManagerSearch: '',
        salesManagerDropdownOpen: false,
        unifiedRows: [],
        unifiedSearch: '',
        unifiedDropdownOpen: false,
        salesManagers: @js(collect($salesManagers ?? [])->map(fn($m) => ['id' => (string)$m->id, 'name' => $m->name])->values()->toArray()),
        selectedPayeeCountry: '',
        selectedSellerId: null,
        selectedBankId: null,
        sellerSearch: '',
        sellerDropdownOpen: false,
        sellers: [],
        bankDetails: [],
        selectedCurrency: '',
        selectedPaymentMode: '',
        countries: @js(collect($countries ?? [])->map(function($c) { return ['id' => $c->id, 'name' => $c->name, 'currency' => $c->currency ?? '$']; })->values()->toArray()),

        get filteredSalesManagers() {
            if (!this.salesManagerSearch) return this.salesManagers;
            const search = this.salesManagerSearch.toLowerCase();
            return this.salesManagers.filter(m => (m.name && m.name.toLowerCase().includes(search)));
        },

        unifiedRowPrimary(r) {
            if (!r) return '';
            return r.proforma_invoice_number || '—';
        },

        unifiedRowSubtitle(r) {
            if (!r) return '—';
            const parts = [];
            if (r.buyer_company_name) parts.push(r.buyer_company_name);
            if (r.buyer_name) parts.push(r.buyer_name);
            if (r.company_name) parts.push(r.company_name);
            return parts.join(' · ') || '—';
        },

        get unifiedSelectedLabel() {
            if (this.lockedSelectionLabel) return this.lockedSelectionLabel;
            return 'Search PI number, buyer, company, or contract #…';
        },

        get filteredUnifiedRows() {
            if (!this.unifiedSearch || !String(this.unifiedSearch).trim()) return this.unifiedRows;
            const search = String(this.unifiedSearch).toLowerCase().trim();
            return this.unifiedRows.filter(r => {
                if (r.proforma_invoice_number && String(r.proforma_invoice_number).toLowerCase().includes(search)) return true;
                if (r.buyer_company_name && String(r.buyer_company_name).toLowerCase().includes(search)) return true;
                if (r.contract_number && String(r.contract_number).toLowerCase().includes(search)) return true;
                if (r.buyer_name && String(r.buyer_name).toLowerCase().includes(search)) return true;
                if (r.company_name && String(r.company_name).toLowerCase().includes(search)) return true;
                return false;
            });
        },

        isUnifiedRowSelected(r) {
            return this.currentPiId != null && String(r.id) === String(this.currentPiId);
        },

        selectSalesManager(id) {
            this.selectedSalesManager = id !== undefined && id !== null ? String(id) : '';
            this.salesManagerDropdownOpen = false;
            this.unifiedRows = [];
            this.unifiedSearch = '';
            this.loadUnifiedRows();
        },

        loadUnifiedRows() {
            const self = this;
            if (!self.selectedSalesManager) {
                self.unifiedRows = [];
                return;
            }
            const url = '{{ route('payments.get-search-rows') }}?sales_manager_id=' + encodeURIComponent(String(self.selectedSalesManager));
            fetch(url)
                .then(r => { if (!r.ok) throw new Error(); return r.json(); })
                .then(data => {
                    self.unifiedRows = Array.isArray(data) ? data : [];
                })
                .catch(() => { self.unifiedRows = []; });
        },

        selectUnified(r) {
            if (!this.selectedSalesManager || !r) return;
            const params = new URLSearchParams();
            params.set('sales_manager', this.selectedSalesManager);
            params.set('proforma_invoice_id', r.id);
            window.location.href = this.redirectBase + '?' + params.toString();
        },

        loadSellers() {
            if (!this.selectedPayeeCountry) {
                this.sellers = [];
                this.selectedSellerId = null;
                this.selectedBankId = null;
                this.bankDetails = [];
                this.selectedCurrency = '';
                return;
            }

            // Get currency from selected country
            const selectedCountry = this.countries.find(c => c.id == this.selectedPayeeCountry);
            this.selectedCurrency = selectedCountry ? (selectedCountry.currency || '$') : '';
            
            fetch('{{ route('payments.get-sellers-by-country') }}?country_id=' + this.selectedPayeeCountry)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.sellers = data || [];
                    this.selectedSellerId = null;
                    this.selectedBankId = null;
                    this.bankDetails = [];
                })
                .catch(error => {
                    console.error('Error loading sellers:', error);
                    this.sellers = [];
                });
        },
        
        get filteredSellers() {
            if (!this.sellerSearch) return this.sellers;
            const search = this.sellerSearch.toLowerCase();
            return this.sellers.filter(s => 
                (s.seller_name && s.seller_name.toLowerCase().includes(search)) ||
                (s.pi_short_name && s.pi_short_name.toLowerCase().includes(search))
            );
        },
        
        selectSeller(sellerId) {
            this.selectedSellerId = sellerId;
            this.selectedBankId = null;
            this.sellerDropdownOpen = false;
            this.loadBankDetails();
        },
        
        loadBankDetails() {
            if (!this.selectedSellerId) {
                this.bankDetails = [];
                this.selectedBankId = null;
                return;
            }
            fetch('{{ route('payments.get-bank-details-by-seller') }}?seller_id=' + this.selectedSellerId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.bankDetails = data || [];
                })
                .catch(error => {
                    console.error('Error loading bank details:', error);
                    this.bankDetails = [];
                });
        }
    }" x-init="loadUnifiedRows()">
        {{-- Page header: same layout as create proforma / other list pages --}}
        <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center flex-wrap gap-2" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-undo text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Return Payment</h1>
                            <p class="text-muted mb-0 small">Return payment for an approved contract or proforma invoice</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-2 mt-lg-0">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-list me-2"></i>Payments
                        </a>
                        <a href="{{ route('payments.return-payment') }}" class="btn btn-outline-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-search me-2"></i>Back to Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Section --}}
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Search</h2>
                        <p class="text-muted small mb-0">Select a sales manager, then pick a proforma invoice (search by PI #, buyer, company, or linked contract #).</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                        <div class="position-relative" @click.away="salesManagerDropdownOpen = false">
                            <button type="button"
                                    @click="salesManagerDropdownOpen = !salesManagerDropdownOpen; unifiedDropdownOpen = false"
                                    class="form-control text-start d-flex justify-content-between align-items-center"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                <span x-text="selectedSalesManager ? salesManagers.find(m => m.id == selectedSalesManager)?.name || 'Select Sales Manager' : 'Select Sales Manager'"></span>
                                <i class="fas fa-chevron-down flex-shrink-0" :class="{ 'rotate-180': salesManagerDropdownOpen }" style="transition: transform 0.2s ease;"></i>
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
                                         :class="{ 'text-white': selectedSalesManager == m.id }"
                                         :style="selectedSalesManager == m.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
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
                        <label class="form-label fw-medium" style="color: #374151;">Proforma invoice</label>
                        <div class="position-relative" @click.away="unifiedDropdownOpen = false">
                            <button type="button"
                                    @click="unifiedDropdownOpen = !unifiedDropdownOpen; salesManagerDropdownOpen = false; if (unifiedDropdownOpen && selectedSalesManager && unifiedRows.length === 0) { loadUnifiedRows(); }"
                                    class="form-control text-start d-flex justify-content-between align-items-center"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                    :disabled="!selectedSalesManager">
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
                                           placeholder="Search PI number, buyer, company, or contract #…"
                                           class="form-control form-control-sm"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <template x-if="filteredUnifiedRows.length === 0">
                                    <div class="p-3 text-center text-muted small">Select a sales manager first, or no rows match your search.</div>
                                </template>
                                <template x-for="r in filteredUnifiedRows" :key="'pi-' + r.id">
                                    <div class="d-flex align-items-center py-2 px-3 gap-2"
                                         @click="selectUnified(r)"
                                         style="cursor: pointer;"
                                         :class="{ 'text-white': isUnifiedRowSelected(r) }"
                                         :style="isUnifiedRowSelected(r) ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                         onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                         onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                        <span class="badge flex-shrink-0 rounded-pill small"
                                              :class="isUnifiedRowSelected(r) ? 'bg-light text-dark' : 'bg-secondary'">PI</span>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-medium text-truncate" x-text="unifiedRowPrimary(r)"></div>
                                            <small class="d-block text-truncate" :class="isUnifiedRowSelected(r) ? 'opacity-90' : 'text-muted'" x-text="unifiedRowSubtitle(r)"></small>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <small class="text-muted">Each row is a proforma invoice; return payment is recorded against that PI.</small>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('payments.return-payment') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-redo me-2"></i>Reset search
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Form (shown when contract or proforma invoice is selected) -->
        @if(isset($contract) || isset($proformaInvoice))
        <div class="card shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="return">
                @if(isset($contract))
                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                @endif
                @if(isset($proformaInvoice))
                    <input type="hidden" name="proforma_invoice_id" value="{{ $proformaInvoice->id }}">
                @endif

                {{-- Top block: header + red separator + PI / Contract / Buyer --}}
                <div class="card-header border-0 pt-4 px-4 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #dc3545;">
                            <i class="fas fa-undo text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Return Payment Details</h2>
                    </div>
                    <div class="mt-3" style="height: 2px; background: #dc3545; border-radius: 1px;"></div>
                </div>
                <div class="card-body pt-4 px-4">
                    <div class="row g-3 mb-4">
                        {{-- PI Number: only when form opened by Proforma Invoice selection; never show when search/select by Contract number --}}
                        @if(isset($proformaInvoice) && !isset($contract) && empty($openedByContract ?? false))
                        <div class="col-md-4">
                            <label class="form-label">PI Number</label>
                            <input type="text" class="form-control" value="{{ $proformaInvoice->proforma_invoice_number }}" readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="form-label">Contract Number</label>
                            <input type="text" class="form-control"
                                   value="{{ isset($contract) ? $contract->contract_number : (isset($proformaInvoice) && $proformaInvoice->contract ? $proformaInvoice->contract->contract_number : 'N/A') }}"
                                   readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Buyer Name</label>
                            <input type="text" class="form-control"
                                   value="{{ isset($contract) ? $contract->buyer_name . ($contract->company_name ? ' (' . $contract->company_name . ')' : '') : (isset($proformaInvoice) ? $proformaInvoice->buyer_company_name : 'N/A') }}"
                                   readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Mode</label>
                            <select name="payment_method" id="payment_method" x-model="selectedPaymentMode" class="form-select">
                                <option value="">Select Payment Mode</option>
                                <option value="UPI">UPI</option>
                                <option value="NEFT">NEFT</option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="CASH">CASH</option>
                                <option value="TT">TT</option>
                                <option value="LC">LC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_by" class="form-label">Payment By</label>
                            <input type="text" name="payment_by" id="payment_by" class="form-control" placeholder="Enter payment by">
                        </div>
                        <div class="col-md-6">
                            <label for="payee_country_id" class="form-label">Payee</label>
                            <select name="payee_country_id" id="payee_country_id" x-model="selectedPayeeCountry" @change="loadSellers()" class="form-select">
                                <option value="">Select Payee (Country)</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_to_seller_id" class="form-label">Payment To</label>
                            <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                <button type="button" 
                                        @click="sellerDropdownOpen = !sellerDropdownOpen"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!selectedPayeeCountry">
                                    <span x-text="selectedSellerId ? sellers.find(s => s.id == selectedSellerId)?.seller_name || 'Select Seller' : 'Select Seller'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }"></i>
                                </button>
                                <div x-show="sellerDropdownOpen" 
                                     x-cloak
                                     class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                     style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important;"
                                     @click.stop>
                                    <div class="p-2 border-bottom">
                                        <input type="text" 
                                               x-model="sellerSearch" 
                                               @click.stop
                                               placeholder="Search seller..."
                                               class="form-control form-control-sm">
                                    </div>
                                    <template x-if="filteredSellers.length === 0">
                                        <div class="p-3 text-center text-muted">No sellers found</div>
                                    </template>
                                    <template x-for="seller in filteredSellers" :key="seller.id">
                                        <div class="d-flex align-items-center py-2 px-3 cursor-pointer hover:bg-gray-100" 
                                             @click="selectSeller(seller.id)"
                                             style="cursor: pointer;"
                                             :class="{ 'bg-primary text-white': selectedSellerId == seller.id }">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium" x-text="seller.seller_name"></div>
                                                <small x-text="seller.pi_short_name"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="payment_to_seller_id" x-model="selectedSellerId">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_detail_id" class="form-label">Bank Name</label>
                            <select name="bank_detail_id" id="bank_detail_id" x-model="selectedBankId" class="form-select" :disabled="!selectedSellerId">
                                <option value="">Select Bank</option>
                                <template x-for="bank in bankDetails" :key="bank.id">
                                    <option :value="bank.id" x-text="bank.bank_name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter transaction ID">
                        </div>
                        <div class="col-md-6" x-show="selectedPayeeCountry && selectedCurrency === '$'" x-cloak>
                            <label for="swift_copy" class="form-label">SWIFT Copy <span class="text-muted">(Image)</span></label>
                            <input type="file" name="swift_copy" id="swift_copy" accept="image/*" class="form-control">
                            <small class="text-muted">Upload SWIFT copy image (JPG, PNG, etc.)</small>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-right: none; border-radius: 8px 0 0 8px; font-weight: 500; min-width: 50px; justify-content: center;" x-text="selectedCurrency || '$'"></span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>Return Payment
                                </button>
                                <a href="{{ route('payments.return-payment') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
        </div>
        @endif
    </div>
    <style>[x-cloak]{display:none !important;}.rotate-180{transform:rotate(180deg);}</style>
</x-app-layout>
