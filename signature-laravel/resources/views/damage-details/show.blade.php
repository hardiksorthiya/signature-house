@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Damage Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('damage-details.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    <!-- Add New Damage Detail Form -->
    @can('create damage')
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Add New Damage Detail</h2>
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

            <form method="POST" action="{{ route('damage-details.store', $proformaInvoice) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-heading me-2"></i>Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               class="form-control" 
                               placeholder="Enter damage title"
                               value="{{ old('title') }}"
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
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('detail') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">
                            <i class="fas fa-images me-2"></i>Images
                        </label>
                        <input type="file" 
                               name="images[]" 
                               id="imageInput"
                               multiple 
                               accept="image/*"
                               class="form-control" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <small class="text-muted">You can select multiple images. Supported formats: JPG, PNG, GIF, WEBP. Max size: 10MB per image.</small>
                    </div>

                    <!-- Image Preview Area -->
                    <div id="imagePreview" class="col-12" style="display: none;">
                        <label class="form-label fw-medium" style="color: #374151;">Preview</label>
                        <div class="row g-3" id="previewContainer">
                            <!-- Preview images will be added here dynamically -->
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Add Damage Detail
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Existing Damage Details Section -->
    @if($damageDetails->count() > 0)
    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Damage Entries ({{ $damageDetails->count() }})</h2>
            </div>
        </div>
        <div class="card-body p-4">
            @foreach($damageDetails as $index => $damage)
            <div class="border rounded p-4 mb-4" style="background-color: #fff5f5;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h5 class="fw-semibold mb-2" style="color: #dc2626;">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $damage->title }}
                        </h5>
                        @if($damage->detail)
                        <p class="text-muted mb-2">{{ $damage->detail }}</p>
                        @endif
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Added: {{ optional($damage->created_at)->format('M d, Y H:i') ?? '—' }}
                            @if($damage->created_at && $damage->updated_at && $damage->updated_at->ne($damage->created_at))
                                | Updated: {{ $damage->updated_at->format('M d, Y H:i') }}
                            @endif
                        </small>
                    </div>
                    @canany(['edit damage', 'delete damage'])
                    <div class="d-flex gap-2">
                        @can('edit damage')
                        <a href="{{ route('damage-details.edit', [$proformaInvoice, $damage]) }}" class="btn btn-sm btn-outline-info" title="Edit Damage Detail">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                        @can('delete damage')
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger" 
                                title="Delete Damage Detail"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteDamageModal{{ $damage->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endcan
                    </div>
                    @endcanany
                </div>

                @if($damage->images->count() > 0)
                <div class="mt-3">
                    <h6 class="fw-medium mb-3" style="color: #374151;">
                        <i class="fas fa-images me-2"></i>Images ({{ $damage->images->count() }})
                    </h6>
                    <div class="row g-3">
                        @foreach($damage->images as $image)
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
                                <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                                    <a href="{{ Storage::url($image->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Full Size">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                    @canany(['edit damage', 'delete damage'])
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete Image"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteDamageImageModal{{ $image->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcanany
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted mb-0">No damage details added yet. Add a new damage detail using the form above.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Damage Detail Modals -->
    @foreach($damageDetails as $damage)
    <div class="modal fade" id="deleteDamageModal{{ $damage->id }}" tabindex="-1" aria-labelledby="deleteDamageModalLabel{{ $damage->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="deleteDamageModalLabel{{ $damage->id }}" style="color: #1f2937;">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Damage Detail
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('damage-details.destroy', $damage) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-muted mb-2">
                            Are you sure you want to delete this damage detail?
                        </p>
                        <p class="fw-medium mb-2" style="color: #dc2626;">{{ $damage->title }}</p>
                        <p class="text-muted small mb-0">
                            This will permanently delete the damage detail and all associated images. This action cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Damage Detail
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Delete Damage Image Modals -->
    @foreach($damageDetails as $damage)
        @foreach($damage->images as $image)
        <div class="modal fade" id="deleteDamageImageModal{{ $image->id }}" tabindex="-1" aria-labelledby="deleteDamageImageModalLabel{{ $image->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold" id="deleteDamageImageModalLabel{{ $image->id }}" style="color: #1f2937;">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Image
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('damage-details.destroy-image', ['damageImage' => $image]) }}" method="POST">
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
    @endforeach

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

