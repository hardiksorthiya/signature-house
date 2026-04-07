<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Payment</h1>
            <p class="text-muted mb-0">Update payment information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View
            </a>
            <a href="{{ route('payments.download-pdf', $payment->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-edit text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">
                    Edit Payment
                    @if($payment->type === 'collect')
                        <span class="badge bg-success ms-2">Collect Payment</span>
                    @else
                        <span class="badge bg-danger ms-2">Return Payment</span>
                    @endif
                </h2>
            </div>
        </div>
        <div class="card-body p-4" x-data="paymentEdit()" x-init="init()">
            <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- PI Number (if proforma invoice is selected) -->
                    @if($payment->proformaInvoice)
                    <div class="col-md-4">
                        <label class="form-label">PI Number</label>
                        <input type="text" class="form-control" value="{{ $payment->proformaInvoice->proforma_invoice_number }}" readonly style="background-color: #f3f4f6;">
                    </div>
                    @endif

                    <!-- Contract Number -->
                    <div class="col-md-4">
                        <label class="form-label">Contract Number</label>
                        <input type="text" class="form-control" 
                               value="{{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? 'N/A') }}" 
                               readonly style="background-color: #f3f4f6;">
                    </div>

                    <!-- Buyer Name -->
                    <div class="col-md-4">
                        <label class="form-label">Buyer Name</label>
                        <input type="text" class="form-control" 
                               value="{{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? 'N/A') }}{{ $payment->contract && $payment->contract->company_name ? ' (' . $payment->contract->company_name . ')' : '' }}" 
                               readonly style="background-color: #f3f4f6;">
                    </div>

                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ $payment->payment_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Payment Mode</label>
                        <select name="payment_method" id="payment_method" x-model="selectedPaymentMode" class="form-select">
                            <option value="">Select Payment Mode</option>
                            <option value="UPI" {{ $payment->payment_method === 'UPI' ? 'selected' : '' }}>UPI</option>
                            <option value="NEFT" {{ $payment->payment_method === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                            <option value="CHEQUE" {{ $payment->payment_method === 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                            <option value="CASH" {{ $payment->payment_method === 'CASH' ? 'selected' : '' }}>CASH</option>
                            <option value="TT" {{ $payment->payment_method === 'TT' ? 'selected' : '' }}>TT</option>
                            <option value="LC" {{ $payment->payment_method === 'LC' ? 'selected' : '' }}>LC</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_by" class="form-label">Payment By</label>
                        <input type="text" name="payment_by" id="payment_by" class="form-control" placeholder="Enter payment by" value="{{ $payment->payment_by }}">
                    </div>
                    <div class="col-md-6">
                        <label for="payee_country_id" class="form-label">Payee</label>
                        <select name="payee_country_id" id="payee_country_id" x-model="selectedPayeeCountry" @change="loadSellers()" class="form-select">
                            <option value="">Select Payee (Country)</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $payment->payee_country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
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
                                <option :value="bank.id" x-text="bank.bank_name" :selected="bank.id == {{ $payment->bank_detail_id ?? 'null' }}"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="transaction_id" class="form-label">Transaction ID</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter transaction ID" value="{{ $payment->transaction_id }}">
                    </div>
                    <div class="col-md-6" x-show="selectedPayeeCountry && selectedCurrency === '$'" x-cloak>
                        <label for="swift_copy" class="form-label">SWIFT Copy <span class="text-muted">(Image)</span></label>
                        <input type="file" name="swift_copy" id="swift_copy" accept="image/*" class="form-control">
                        @if($payment->swift_copy)
                            <small class="text-muted d-block mt-1">Current: <a href="{{ Storage::disk('public')->url($payment->swift_copy) }}" target="_blank">View current SWIFT copy</a></small>
                        @else
                            <small class="text-muted">Upload SWIFT copy image (JPG, PNG, etc.)</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-right: none; border-radius: 8px 0 0 8px; font-weight: 500; min-width: 50px; justify-content: center;" x-text="selectedCurrency || '$'"></span>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $payment->amount }}" required style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ $payment->notes }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Payment
                            </button>
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('paymentEdit', () => ({
        selectedPayeeCountry: '{{ $payment->payee_country_id ?? '' }}',
        selectedSellerId: {{ $payment->payment_to_seller_id ?? 'null' }},
        selectedBankId: {{ $payment->bank_detail_id ?? 'null' }},
        sellerSearch: '',
        sellerDropdownOpen: false,
        sellers: @js($sellers),
        bankDetails: @js($bankDetails),
        selectedCurrency: '{{ $payment->payeeCountry && $payment->payeeCountry->currency ? $payment->payeeCountry->currency : '$' }}',
        selectedPaymentMode: '{{ $payment->payment_method ?? '' }}',
        countries: @js(collect($countries)->map(function($c) { return ['id' => $c->id, 'name' => $c->name, 'currency' => $c->currency ?? '$']; })->values()->toArray()),
        
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
        
        loadSellers() {
            if (!this.selectedPayeeCountry) {
                this.sellers = [];
                this.selectedSellerId = null;
                this.selectedBankId = null;
                this.bankDetails = [];
                this.selectedCurrency = '';
                return;
            }
            
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
                    if (this.selectedSellerId && !this.sellers.find(s => s.id == this.selectedSellerId)) {
                        this.selectedSellerId = null;
                        this.selectedBankId = null;
                        this.bankDetails = [];
                    } else if (this.selectedSellerId) {
                        this.loadBankDetails();
                    }
                })
                .catch(error => {
                    console.error('Error loading sellers:', error);
                    this.sellers = [];
                });
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
                    if (this.selectedBankId && !this.bankDetails.find(b => b.id == this.selectedBankId)) {
                        this.selectedBankId = null;
                    }
                })
                .catch(error => {
                    console.error('Error loading bank details:', error);
                    this.bankDetails = [];
                });
        },
        
        init() {
            if (this.selectedPayeeCountry) {
                this.loadSellers();
            }
        }
    }));
});
</script>

