<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Purchase Order Details</h1>
            <p class="text-muted mb-0">View purchase order: {{ $purchaseOrder->purchase_order_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-shopping-bag text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Purchase Order Information</h2>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Basic Information -->
                <div class="col-12">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Basic Information</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">PO Number</label>
                    <div class="form-control-plaintext fw-semibold" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->purchase_order_number }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Proforma Invoice</label>
                    <div class="form-control-plaintext" style="padding: 0.5rem 0;">
                        @if($purchaseOrder->proformaInvoice)
                            <a href="{{ route('proforma-invoices.show', $purchaseOrder->proformaInvoice) }}" class="text-decoration-none">
                                {{ $purchaseOrder->proformaInvoice->proforma_invoice_number }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Buyer Name</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->buyer_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium text-muted">Address</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0; white-space: pre-wrap;">
                        {{ $purchaseOrder->address ?? 'N/A' }}
                    </div>
                </div>

                <!-- Shipping Details -->
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Shipping Details</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">No of Bill</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->no_of_bill ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">No of Container</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->no_of_container ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Size of Container</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->size_of_container ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Port of Destination</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        @if($purchaseOrder->portOfDestination)
                            {{ $purchaseOrder->portOfDestination->name }}
                            @if($purchaseOrder->portOfDestination->code)
                                ({{ $purchaseOrder->portOfDestination->code }})
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <!-- Proforma Invoice Details -->
                @if($purchaseOrder->proformaInvoice)
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Proforma Invoice Details</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Customer Name</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->proformaInvoice->buyer_company_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Seller</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->proformaInvoice->seller->seller_name ?? 'N/A' }}
                    </div>
                </div>

                @if($purchaseOrder->proformaInvoice->contract)
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Contract Number</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->proformaInvoice->contract->contract_number ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Sales Manager</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->proformaInvoice->contract->creator->name ?? 'N/A' }}
                    </div>
                </div>
                @endif
                @endif

                <!-- Attachments -->
                @if($purchaseOrder->attachments->count() > 0)
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Attachments</h5>
                    <div class="list-group">
                        @foreach($purchaseOrder->attachments as $attachment)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file me-2"></i>
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                    {{ $attachment->file_name }}
                                </a>
                                <small class="text-muted ms-2">({{ number_format($attachment->file_size / 1024, 2) }} KB)</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($purchaseOrder->notes)
                <div class="col-12 mt-4">
                    <label class="form-label fw-medium text-muted">Notes</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0; white-space: pre-wrap;">
                        {{ $purchaseOrder->notes }}
                    </div>
                </div>
                @endif

                <!-- Metadata -->
                <div class="col-12 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Metadata</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Created By</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->creator->name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Created At</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted">Updated At</label>
                    <div class="form-control-plaintext" style="color: #1f2937; padding: 0.5rem 0;">
                        {{ $purchaseOrder->updated_at->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
