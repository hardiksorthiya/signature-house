<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Payment Details</h1>
            <p class="text-muted mb-0">View payment information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('payments.download-pdf', $payment->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">
                    Payment Information
                    @if($payment->type === 'collect')
                        <span class="badge bg-success ms-2">Collect Payment</span>
                    @else
                        <span class="badge bg-danger ms-2">Return Payment</span>
                    @endif
                </h2>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Basic Information -->
                <div class="col-12">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">Basic Information</h5>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Payment Date</label>
                    <div class="fw-medium">{{ $payment->payment_date->format('M d, Y') }}</div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Payment Mode</label>
                    <div>
                        <span class="badge bg-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Amount</label>
                    <div class="fw-semibold fs-5" style="{{ $payment->type === 'collect' ? 'color: #10b981;' : 'color: #dc2626;' }}">
                        {{ $payment->payeeCountry && $payment->payeeCountry->currency ? $payment->payeeCountry->currency : '$' }}{{ number_format($payment->amount, 2) }}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Payment By</label>
                    <div>{{ $payment->payment_by ?? 'N/A' }}</div>
                </div>

                <!-- Contract/Proforma Invoice Information -->
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">Contract/Proforma Invoice</h5>
                </div>
                
                @if($payment->proformaInvoice)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">PI Number</label>
                    <div>{{ $payment->proformaInvoice->proforma_invoice_number }}</div>
                </div>
                @endif
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Contract Number</label>
                    <div>{{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? 'N/A') }}</div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Buyer Name</label>
                    <div>
                        {{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? 'N/A') }}
                        @if($payment->contract && $payment->contract->company_name)
                            <br><small class="text-muted">({{ $payment->contract->company_name }})</small>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                @if($payment->payeeCountry || $payment->paymentToSeller || $payment->bankDetail)
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">Payment Details</h5>
                </div>
                
                @if($payment->payeeCountry)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Payee (Country)</label>
                    <div>{{ $payment->payeeCountry->name }}</div>
                </div>
                @endif
                
                @if($payment->paymentToSeller)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Payment To (Seller)</label>
                    <div>{{ $payment->paymentToSeller->seller_name }}</div>
                </div>
                @endif
                
                @if($payment->bankDetail)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Bank Name</label>
                    <div>{{ $payment->bankDetail->bank_name }}</div>
                </div>
                @endif
                
                @if($payment->transaction_id)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Transaction ID</label>
                    <div>{{ $payment->transaction_id }}</div>
                </div>
                @endif
                @endif

                <!-- SWIFT Copy -->
                @if($payment->swift_copy)
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">SWIFT Copy</h5>
                    <div>
                        <img src="{{ Storage::disk('public')->url($payment->swift_copy) }}" alt="SWIFT Copy" class="img-fluid" style="max-width: 600px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($payment->notes)
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">Notes</h5>
                    <div class="p-3 bg-light rounded">{{ $payment->notes }}</div>
                </div>
                @endif

                <!-- Created Information -->
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">Created Information</h5>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Created By</label>
                    <div>{{ $payment->creator->name ?? 'N/A' }}</div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Created At</label>
                    <div>{{ $payment->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
