@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Image Uploading</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ms-unloading-images.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-images text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Upload Images</h2>
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

            <!-- Upload Form -->
            @canany(['create image uploading', 'edit image uploading'])
            <form method="POST" action="{{ route('ms-unloading-images.store', $proformaInvoice) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-medium" style="color: #374151;">
                        <i class="fas fa-upload me-2"></i>Select Multiple Images
                    </label>
                    <input type="file" 
                           name="images[]" 
                           id="imageInput"
                           multiple 
                           accept="image/*"
                           class="form-control" 
                           style="border-radius: 8px; border: 1px solid #e5e7eb;"
                           required>
                    <small class="text-muted">You can select multiple images at once. Supported formats: JPG, PNG, GIF, WEBP. Max size: 10MB per image.</small>
                </div>

                <!-- Image Preview Area -->
                <div id="imagePreview" class="row g-3 mb-4" style="display: none;">
                    <!-- Preview images will be added here dynamically -->
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('ms-unloading-images.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Images
                    </button>
                </div>
            </form>
            @endcanany

            <!-- Existing Images Section -->
            @if($existingImages->count() > 0)
            <div class="border-top pt-4 mt-4">
                <h5 class="fw-semibold mb-3" style="color: #1f2937;">
                    <i class="fas fa-images me-2"></i>Uploaded Images ({{ $existingImages->count() }})
                </h5>
                <div class="row g-3">
                    @foreach($existingImages as $image)
                    <div class="col-md-3">
                        <div class="card border position-relative">
                            <a href="{{ Storage::url($image->file_path) }}" target="_blank" class="text-decoration-none">
                                <img src="{{ Storage::url($image->file_path) }}" 
                                     class="card-img-top" 
                                     style="height: 200px; object-fit: cover; cursor: pointer;" 
                                     alt="{{ $image->file_name }}"
                                     onerror="this.src='{{ asset('images/placeholder.png') }}'">
                            </a>
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">
                                    {{ $image->file_name }}
                                </small>
                                <small class="text-muted">{{ number_format($image->file_size / 1024, 2) }} KB</small>
                                <br>
                                <small class="text-muted">{{ $image->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                                <a href="{{ Storage::url($image->file_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="View Full Size">
                                    <i class="fas fa-expand"></i>
                                </a>
                                @canany(['edit image uploading', 'delete image uploading'])
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Delete Image"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteImageModal{{ $image->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcanany
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="border-top pt-4 mt-4">
                <div class="text-center py-4">
                    <i class="fas fa-images fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                    <p class="text-muted mb-0">No images uploaded yet. Upload images using the form above.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Image Modals -->
    @foreach($existingImages as $image)
    <div class="modal fade" id="deleteImageModal{{ $image->id }}" tabindex="-1" aria-labelledby="deleteImageModalLabel{{ $image->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="deleteImageModalLabel{{ $image->id }}" style="color: #1f2937;">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Image
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ms-unloading-images.destroy', $image) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-muted mb-0">
                            Are you sure you want to delete this image? <strong>{{ $image->file_name }}</strong>
                        </p>
                        <p class="text-muted small mt-2 mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script>
        // Image preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('imageInput');
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
                                            <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Preview">
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

