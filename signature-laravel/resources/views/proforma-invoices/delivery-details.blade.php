@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Delivery Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('proforma-invoices.delivery-details-view', $proformaInvoice) }}" class="btn btn-outline-primary">
                <i class="fas fa-file-alt me-2"></i>View summary
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to PI Details
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-truck text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Delivery Documents</h2>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('proforma-invoices.store-delivery-details', $proformaInvoice) }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Delivery Details Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th style="width: 5%;" class="text-center">Check</th>
                                <th style="width: 5%;" class="text-center">S.No</th>
                                <th style="width: 30%;">Delivery Document</th>
                                <th style="width: 18%;">Date</th>
                                <th style="width: 22%;">Number</th>
                                <th style="width: 20%;">No. of Copies</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveryDocuments as $index => $documentName)
                                @php
                                    $existingDetail = $existingDetails->get($documentName);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               name="delivery_details[{{ $index }}][is_received]" 
                                               value="1"
                                               {{ $existingDetail && $existingDetail->is_received ? 'checked' : '' }}
                                               class="form-check-input" 
                                               style="width: 20px; height: 20px; cursor: pointer;">
                                    </td>
                                    <td class="text-center fw-medium">{{ $index + 1 }}</td>
                                    <td class="fw-medium" style="color: #374151;">
                                        {{ $documentName }}
                                    </td>
                                    <td>
                                        <input type="date" 
                                               name="delivery_details[{{ $index }}][date]" 
                                               value="{{ $existingDetail ? ($existingDetail->date ? $existingDetail->date->format('Y-m-d') : '') : '' }}"
                                               class="form-control form-control-sm" 
                                               style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="delivery_details[{{ $index }}][document_number]" 
                                               value="{{ $existingDetail ? $existingDetail->document_number : '' }}"
                                               placeholder="Enter document number" 
                                               class="form-control form-control-sm" 
                                               style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="delivery_details[{{ $index }}][no_of_copies]" 
                                               value="{{ $existingDetail ? $existingDetail->no_of_copies : '' }}"
                                               placeholder="Copies" 
                                               min="0"
                                               class="form-control form-control-sm" 
                                               style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                    </td>
                                    <input type="hidden" name="delivery_details[{{ $index }}][document_name]" value="{{ $documentName }}">
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Image Upload Section -->
                <div class="border-top pt-4 mt-4">
                    <h5 class="fw-semibold mb-3" style="color: #1f2937;">
                        <i class="fas fa-images me-2"></i>Upload Images
                    </h5>
                    <div class="mb-3">
                        <label class="form-label fw-medium" style="color: #374151;">Select Multiple Images</label>
                        <input type="file" 
                               name="images[]" 
                               multiple 
                               accept="image/*"
                               class="form-control" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <small class="text-muted">You can select multiple images at once. Supported formats: JPG, PNG, GIF. Max size: 10MB per image.</small>
                    </div>
                    
                    <!-- Image Preview Area -->
                    <div id="imagePreview" class="row g-3 mt-3" style="display: none;">
                        <!-- Preview images will be added here dynamically -->
                    </div>
                    
                    <!-- Existing Images -->
                    @if(isset($existingImages) && $existingImages->count() > 0)
                    <div class="mt-4">
                        <h6 class="fw-medium mb-3" style="color: #374151;">Previously Uploaded Images</h6>
                        <div class="row g-3">
                            @foreach($existingImages as $image)
                            <div class="col-md-3">
                                <div class="card border position-relative">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         class="card-img-top" 
                                         style="height: 150px; object-fit: cover;" 
                                         alt="{{ $image->file_name }}"
                                         onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">
                                            {{ $image->file_name }}
                                        </small>
                                        <small class="text-muted">{{ number_format($image->file_size / 1024, 2) }} KB</small>
                                    </div>
                                    <a href="{{ Storage::url($image->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info position-absolute top-0 end-0 m-2" 
                                       title="View Full Size">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Delivery Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.querySelector('input[name="images[]"]');
            const previewContainer = document.getElementById('imagePreview');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    previewContainer.innerHTML = '';
                    
                    if (files.length > 0) {
                        previewContainer.style.display = 'block';
                        
                        Array.from(files).forEach((file, index) => {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const col = document.createElement('div');
                                    col.className = 'col-md-3';
                                    col.innerHTML = `
                                        <div class="card border position-relative">
                                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block text-truncate">${file.name}</small>
                                                <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                                            </div>
                                        </div>
                                    `;
                                    previewContainer.appendChild(col);
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    } else {
                        previewContainer.style.display = 'none';
                    }
                });
            }
        });
    </script>
</x-app-layout>

