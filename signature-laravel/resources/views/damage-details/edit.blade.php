@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Damage Detail</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('damage-details.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-edit text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Edit Damage Detail</h2>
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

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('damage-details.update', [$proformaInvoice, $damageDetail]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-heading me-2"></i>Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               class="form-control" 
                               placeholder="Enter damage title"
                               value="{{ old('title', $damageDetail->title) }}"
                               required
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-align-left me-2"></i>Detail
                        </label>
                        <textarea name="detail" 
                                  class="form-control" 
                                  rows="4"
                                  placeholder="Enter damage details"
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('detail', $damageDetail->detail) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-images me-2"></i>Add More Images
                        </label>
                        <input type="file" 
                               name="images[]" 
                               id="imageInput"
                               multiple 
                               accept="image/*"
                               class="form-control" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <small class="text-muted">You can select multiple images to add. Supported formats: JPG, PNG, GIF, WEBP. Max size: 10MB per image.</small>
                    </div>

                    <!-- Image Preview Area -->
                    <div id="imagePreview" class="col-12" style="display: none;">
                        <label class="form-label fw-medium" style="color: #374151;">Preview New Images</label>
                        <div class="row g-3" id="previewContainer">
                            <!-- Preview images will be added here dynamically -->
                        </div>
                    </div>

                    <!-- Existing Images Section -->
                    @if($damageDetail->images->count() > 0)
                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-images me-2"></i>Existing Images ({{ $damageDetail->images->count() }})
                        </label>
                        <div class="row g-3">
                            @foreach($damageDetail->images as $image)
                            <div class="col-md-3">
                                <div class="card border position-relative">
                                    <a href="{{ Storage::url($image->file_path) }}" target="_blank" class="text-decoration-none">
                                        <img src="{{ Storage::url($image->file_path) }}" 
                                             class="card-img-top" 
                                             style="height: 150px; object-fit: cover; cursor: pointer;" 
                                             alt="{{ $image->file_name }}"
                                             onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    </a>
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">
                                            {{ $image->file_name }}
                                        </small>
                                        <small class="text-muted">{{ $image->file_size !== null ? number_format($image->file_size / 1024, 2) . ' KB' : '—' }}</small>
                                    </div>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <a href="{{ Storage::url($image->file_path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Full Size">
                                            <i class="fas fa-expand"></i>
                                        </a>
                                        <form action="{{ route('damage-details.destroy-image', ['damageImage' => $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Image">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('damage-details.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Damage Detail
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('imageInput');
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    previewContainer.innerHTML = '';
                    
                    if (files.length > 0) {
                        imagePreview.style.display = 'block';
                        
                        Array.from(files).forEach((file, index) => {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const col = document.createElement('div');
                                    col.className = 'col-md-3';
                                    col.innerHTML = `
                                        <div class="card border">
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
                        imagePreview.style.display = 'none';
                    }
                });
            }
        });
    </script>
</x-app-layout>

