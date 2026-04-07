<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Business Firm Management</h1>
                <p class="text-muted mb-0 small">Manage business firms with logo and address</p>
            </div>
        </div>
    </div>

    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('business-firms.index') }}" id="businessFirmFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or address..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('businessFirmFilterForm').submit();">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('business-firms.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4" x-data="{ 
        editingFirm: null, 
        isEditing: false,
        editFirm(firm) {
            this.editingFirm = firm;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingFirm = null;
            this.isEditing = false;
        }
    }">
        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-building'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Business Firm' : 'Add Business Firm'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('business-firms.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Business Firm Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter business firm name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Logo</label>
                                <input type="file" name="logo" accept="image/*"
                                       class="form-control @error('logo') is-invalid @enderror"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                       onchange="previewImage(this, 'add-preview')">
                                @error('logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="add-preview" class="mt-2" style="display: none;">
                                    <img src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Address</label>
                                <textarea name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter address"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add Business Firm
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingFirm">
                            <form :action="`{{ url('business-firms') }}/${editingFirm.id}`" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Business Firm Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required
                                           x-model="editingFirm.name"
                                           class="form-control"
                                           placeholder="Enter business firm name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Logo</label>
                                    <input type="file" name="logo" accept="image/*"
                                           class="form-control"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                           onchange="previewImage(this, 'edit-preview')">
                                    <div class="mt-2" x-show="editingFirm.logo">
                                        <small class="text-muted">Current logo:</small>
                                        <div>
                                            <img :src="`{{ asset('public') }}/${editingFirm.logo}`" alt="Current Logo" class="img-thumbnail mt-1" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                    <div id="edit-preview" class="mt-2" style="display: none;">
                                        <small class="text-muted">New logo preview:</small>
                                        <div>
                                            <img src="" alt="Preview" class="img-thumbnail mt-1" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Address</label>
                                    <textarea name="address" rows="3"
                                              x-model="editingFirm.address"
                                              class="form-control"
                                              placeholder="Enter address"
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Business Firm List -->
        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Business Firm List">Business Firm List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $businessFirms->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('business-firms.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('business-firms.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name or address..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                            </div>
                            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Logo</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Address</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businessFirms as $firm)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $businessFirms->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            @if($firm->logo)
                                                <img src="{{ Storage::url($firm->logo) }}" alt="{{ $firm->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="rounded-circle bg-red-100 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-building text-red-600"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $firm->name }}</div>
                                        </td>
                                        <td class="px-2">
                                            <small class="text-muted">{{ Str::limit($firm->address, 50) }}</small>
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        @click="editFirm({
                                                            id: {{ $firm->id }},
                                                            name: '{{ addslashes($firm->name) }}',
                                                            address: '{{ addslashes($firm->address ?? '') }}',
                                                            logo: '{{ $firm->logo }}'
                                                        })"
                                                        class="btn btn-sm btn-outline-info" 
                                                        style="border-radius: 6px;" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('business-firms.destroy', $firm) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-building fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0">No business firms found.</p>
                                                <small class="text-muted mt-1">Add your first business firm to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($businessFirms->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $businessFirms->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    preview.querySelector('img').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    </div>
</x-app-layout>




