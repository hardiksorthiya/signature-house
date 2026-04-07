<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Purchase Order</h1>
            <p class="text-muted mb-0">Edit purchase order: {{ $purchaseOrder->purchase_order_number }}</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-shopping-bag text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Purchase Order Details</h2>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Basic Information</h5>
                    </div>

                    <div class="col-md-6">
                        <label for="purchase_order_number" class="form-label fw-medium">PO Number</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $purchaseOrder->purchase_order_number }}" 
                               readonly
                               style="border-radius: 8px; border: 1px solid #e5e7eb; background-color: #f3f4f6;">
                    </div>

                    <div class="col-md-6">
                        <label for="buyer_name" class="form-label fw-medium">Buyer Name <span class="text-danger">*</span></label>
                        <input type="text" name="buyer_name" id="buyer_name" 
                               class="form-control @error('buyer_name') is-invalid @enderror" 
                               value="{{ old('buyer_name', $purchaseOrder->buyer_name) }}" 
                               required
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('buyer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Enter address"
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address', $purchaseOrder->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Shipping Details -->
                    <div class="col-12 mt-4">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Shipping Details</h5>
                    </div>

                    <div class="col-md-6">
                        <label for="no_of_bill" class="form-label fw-medium">No of Bill</label>
                        <input type="number" name="no_of_bill" id="no_of_bill" 
                               class="form-control @error('no_of_bill') is-invalid @enderror" 
                               value="{{ old('no_of_bill', $purchaseOrder->no_of_bill) }}" 
                               min="0"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('no_of_bill')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="no_of_container" class="form-label fw-medium">No of Container</label>
                        <input type="number" name="no_of_container" id="no_of_container" 
                               class="form-control @error('no_of_container') is-invalid @enderror" 
                               value="{{ old('no_of_container', $purchaseOrder->no_of_container) }}" 
                               min="0"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('no_of_container')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="size_of_container" class="form-label fw-medium">Size of Container</label>
                        <input type="text" name="size_of_container" id="size_of_container" 
                               class="form-control @error('size_of_container') is-invalid @enderror" 
                               value="{{ old('size_of_container', $purchaseOrder->size_of_container) }}" 
                               placeholder="e.g., 20ft, 40ft"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('size_of_container')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="port_of_destination_id" class="form-label fw-medium">Port of Destination</label>
                        <select name="port_of_destination_id" id="port_of_destination_id" 
                                class="form-select @error('port_of_destination_id') is-invalid @enderror"
                                style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">Select Port of Destination</option>
                            @foreach($portOfDestinations as $port)
                                <option value="{{ $port->id }}" {{ old('port_of_destination_id', $purchaseOrder->port_of_destination_id) == $port->id ? 'selected' : '' }}>
                                    {{ $port->name }} @if($port->code)({{ $port->code }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('port_of_destination_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Existing Attachments -->
                    @if($purchaseOrder->attachments->count() > 0)
                    <div class="col-12 mt-4">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Existing Attachments</h5>
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
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteAttachment({{ $attachment->id }}, '{{ route('purchase-orders.delete-attachment', $attachment) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Attachments -->
                    <div class="col-12 mt-4">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Add New Attachments</h5>
                    </div>

                    <div class="col-12">
                        <label for="attachments" class="form-label fw-medium">Upload Files</label>
                        <input type="file" name="attachments[]" id="attachments" 
                               class="form-control @error('attachments.*') is-invalid @enderror" 
                               multiple
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <small class="text-muted">You can select multiple files. Accepted formats: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR (Max 10MB per file)</small>
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12 mt-4">
                        <label for="notes" class="form-label fw-medium">Notes</label>
                        <textarea name="notes" id="notes" rows="4" 
                                  class="form-control @error('notes') is-invalid @enderror"
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Purchase Order
                            </button>
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden form for deleting attachments (outside main form) -->
    <form id="deleteAttachmentForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function deleteAttachment(attachmentId, deleteUrl) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                const form = document.getElementById('deleteAttachmentForm');
                form.action = deleteUrl;
                form.submit();
            }
        }
    </script>
</x-app-layout>
