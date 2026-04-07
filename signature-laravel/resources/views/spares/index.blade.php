@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    {{-- Page header: list-card style like category --}}
    <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center flex-wrap gap-2" style="min-width: 0;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-puzzle-piece text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Spare Management</h1>
                        <p class="text-muted mb-0 small">Manage spares and their related machine categories</p>
                        <span class="badge mt-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $spares->total() }} Total</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-2 mt-lg-0">
                    <a href="{{ route('spares.download-template') }}" class="btn btn-outline-info d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-download me-2"></i>Download CSV Template
                    </a>
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal" style="border-radius: 8px;">
                        <i class="fas fa-file-csv me-2"></i>Import CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Sidebar Overlay --}}
    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    {{-- Filter Sidebar --}}
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('spares.index') }}" id="spareFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or description..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Machine Category</label>
                    <select name="category_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Spare Type</label>
                    <select name="spare_type" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Types</option>
                        <option value="mechanical" {{ request('spare_type') == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                        <option value="electrical" {{ request('spare_type') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('spares.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4" x-data="spareApp()">

        <!-- Left Side: Add/Edit Spare Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-puzzle-piece'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Spare' : 'Add Spare'"></h2>
                    </div>

                    <!-- ADD FORM -->
                    <div x-show="!isEditing">
                        <form action="{{ route('spares.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Spare Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       value="{{ old('name') }}"
                                       placeholder="Enter spare name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Machine Category <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }"></i>
                                    </button>
                                    <div x-show="categoryDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($categories ?? [] as $category)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isCategorySelected({{ $category->id }}) ? 'bg-red-50' : ''"
                                                 :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleCategory({{ $category->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isCategorySelected({{ $category->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleCategory({{ $category->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $category->name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No categories available. Add categories first.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="categoryId in selectedCategories" :key="categoryId">
                                        <input type="hidden" :name="`categories[]`" :value="categoryId">
                                    </template>
                                </div>
                                @error('categories')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Quantity per Machine</label>
                                <input type="number" name="quantity_per_machine" class="form-control"
                                       min="0" step="1" value="{{ old('quantity_per_machine', 1) }}"
                                       placeholder="e.g. 1"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('quantity_per_machine')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Image</label>
                                <input type="file" 
                                       name="image" 
                                       id="imageInput"
                                       accept="image/*"
                                       class="form-control" 
                                       @change="previewImage($event)"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                <textarea name="description" class="form-control" rows="2"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Spare Type <span class="text-danger">*</span></label>
                                <select name="spare_type" class="form-select" required
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Type</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="electrical">Electrical</option>
                                </select>
                                @error('spare_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Stock Quantity</label>
                                <input type="number" name="quantity" class="form-control"
                                       min="0" step="1" value="{{ old('quantity', 0) }}"
                                       placeholder="Inventory quantity"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Sellers <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                    <button type="button" 
                                            @click="sellerDropdownOpen = !sellerDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedSellers.length > 0 ? selectedSellers.length + ' seller(s) selected' : 'Select Sellers'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="sellerDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($sellers ?? [] as $seller)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isSellerSelected({{ $seller->id }}) ? 'bg-red-50' : ''"
                                                 :style="isSellerSelected({{ $seller->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleSeller({{ $seller->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isSellerSelected({{ $seller->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleSeller({{ $seller->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $seller->seller_name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isSellerSelected({{ $seller->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No sellers available. Add sellers first.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="sellerId in selectedSellers" :key="sellerId">
                                        <input type="hidden" :name="`sellers[]`" :value="sellerId">
                                    </template>
                                </div>
                                @error('sellers')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add Spare
                            </button>
                        </form>
                    </div>

                    <!-- EDIT FORM -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingSpare">
                        <form :action="`{{ url('spares') }}/${editingSpare.id}`" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Spare Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" x-model="editingSpare.name" class="form-control" required
                                       placeholder="Enter spare name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Machine Category <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }"></i>
                                    </button>
                                    <div x-show="categoryDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($categories ?? [] as $category)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isCategorySelected({{ $category->id }}) ? 'bg-red-50' : ''"
                                                 :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleCategory({{ $category->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isCategorySelected({{ $category->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleCategory({{ $category->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $category->name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No categories available</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="categoryId in selectedCategories" :key="categoryId">
                                        <input type="hidden" :name="`categories[]`" :value="categoryId">
                                    </template>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Quantity per Machine</label>
                                <input type="number" name="quantity_per_machine" x-model="editingSpare.quantity_per_machine" class="form-control"
                                       min="0" step="1" placeholder="e.g. 1"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Image</label>
                                <input type="file" 
                                       name="image" 
                                       id="editImageInput"
                                       accept="image/*"
                                       class="form-control" 
                                       @change="previewEditImage($event)"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div id="editImagePreview" class="mt-2">
                                    <img :src="editingSpare.image_url" alt="Current Image" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;" x-show="editingSpare.image_url">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                <textarea name="description" x-model="editingSpare.description" class="form-control" rows="3"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Spare Type <span class="text-danger">*</span></label>
                                <select name="spare_type" x-model="editingSpare.spare_type" class="form-select" required
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Type</option>
                                    <option value="mechanical">Mechanical</option>
                                    <option value="electrical">Electrical</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Stock Quantity</label>
                                <input type="number" name="quantity" x-model="editingSpare.quantity" class="form-control" required
                                       min="0" step="1"
                                       placeholder="Inventory quantity"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Sellers <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                    <button type="button" 
                                            @click="sellerDropdownOpen = !sellerDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedSellers.length > 0 ? selectedSellers.length + ' seller(s) selected' : 'Select Sellers'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                    </button>
                                    <div x-show="sellerDropdownOpen" 
                                         x-cloak
                                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                         @click.stop>
                                        @forelse($sellers ?? [] as $seller)
                                            <div class="d-flex align-items-center py-2 px-3" 
                                                 x-data="{ hovered: false }"
                                                 :class="isSellerSelected({{ $seller->id }}) ? 'bg-red-50' : ''"
                                                 :style="isSellerSelected({{ $seller->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                 style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                 @mouseenter="hovered = true"
                                                 @mouseleave="hovered = false"
                                                 @click="toggleSeller({{ $seller->id }})">
                                                <input class="form-check-input me-3" 
                                                       type="checkbox" 
                                                       :checked="isSellerSelected({{ $seller->id }})"
                                                       style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                       @click.stop="toggleSeller({{ $seller->id }})">
                                                <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                    {{ $seller->seller_name }}
                                                </label>
                                                <i class="fas fa-check text-primary ms-2" x-show="isSellerSelected({{ $seller->id }})" style="font-size: 0.875rem;"></i>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted">
                                                <small>No sellers available</small>
                                            </div>
                                        @endforelse
                                    </div>
                                    <template x-for="sellerId in selectedSellers" :key="sellerId">
                                        <input type="hidden" :name="`sellers[]`" :value="sellerId">
                                    </template>
                                </div>
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

        <!-- Right Side: Spare List Table (70%) -->
        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Spare List">Spare List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $spares->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="$parent.filterSidebarOpen = !$parent.filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'category_id', 'spare_type', 'sort']) && (request('search') || request('category_id') || request('spare_type') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('spares.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('spares.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name or description..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                            </div>
                            @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                            @if(request('spare_type'))<input type="hidden" name="spare_type" value="{{ request('spare_type') }}">@endif
                            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" style="min-width: 1000px;">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 80px;">Image</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; min-width: 180px;">Spare Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; min-width: 180px;">Machine Categories</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 110px;">Qty per Machine</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 120px;">Type</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 90px;">Stock</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; min-width: 160px;">Sellers</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 110px;">Created</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; border-bottom: 1px solid #d8b4fe !important; width: 120px; position: sticky; right: 0; background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spares as $spare)
                                    <tr class="border-bottom" style="transition: all 0.2s ease; background-color: white;">
                                        <td class="px-2">
                                            @if($spare->image)
                                                <img src="{{ Storage::url($spare->image) }}" 
                                                     alt="{{ $spare->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                                     onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; font-weight: 500; font-size: 20px;">
                                                    {{ strtoupper(substr($spare->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            <div>
                                                <div class="fw-medium" style="color: #1f2937; font-size: 0.95rem;">{{ $spare->name }}</div>
                                                @if($spare->description)
                                                <small class="text-muted" style="font-size: 0.75rem; display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $spare->description }}">{{ $spare->description }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-2">
                                            @if($spare->machineCategories->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($spare->machineCategories as $category)
                                                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                            {{ $category->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">No categories</small>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            <span class="badge bg-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                {{ $spare->quantity_per_machine ?? 1 }}
                                            </span>
                                        </td>
                                        <td class="px-2">
                                            <span class="badge" 
                                                  style="background-color: {{ $spare->spare_type == 'mechanical' ? '#3b82f6' : '#10b981' }}; color: white; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                {{ ucfirst($spare->spare_type) }}
                                            </span>
                                        </td>
                                        <td class="px-2">
                                            <span class="badge bg-info" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                {{ $spare->quantity ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-2">
                                            @if($spare->sellers->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($spare->sellers as $seller)
                                                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                            {{ $seller->seller_name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">No sellers</small>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt me-2 text-muted" style="font-size: 0.75rem;"></i>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ $spare->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3" style="position: sticky; right: 0; background-color: white; z-index: 10; box-shadow: -2px 0 5px rgba(0,0,0,0.1);">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editSpare({
                                                            id: {{ $spare->id }},
                                                            name: @js($spare->name),
                                                            description: @js($spare->description ?? ''),
                                                            spare_type: @js($spare->spare_type),
                                                            quantity: {{ $spare->quantity ?? 0 }},
                                                            quantity_per_machine: {{ $spare->quantity_per_machine ?? 1 }},
                                                            image_url: @js($spare->image ? Storage::url($spare->image) : ''),
                                                            sellers: @js($spare->sellers),
                                                            categories: @js($spare->machineCategories)
                                                        })"
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="Edit Spare">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('spares.destroy', $spare) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this spare?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Spare">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-puzzle-piece fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No spares found.</p>
                                                <small class="text-muted mt-1">Add your first spare to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($spares->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $spares->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function spareApp() {
                return {
                    editingSpare: null,
                    isEditing: false,
                    selectedSellers: [],
                    sellerDropdownOpen: false,
                    selectedCategories: [],
                    categoryDropdownOpen: false,
            
                    toggleSeller(id) {
                        id = String(id);
                        const index = this.selectedSellers.indexOf(id);
                        index > -1
                            ? this.selectedSellers.splice(index, 1)
                            : this.selectedSellers.push(id);
                    },
            
                    isSellerSelected(id) {
                        return this.selectedSellers.includes(String(id));
                    },

                    toggleCategory(id) {
                        id = String(id);
                        const index = this.selectedCategories.indexOf(id);
                        index > -1
                            ? this.selectedCategories.splice(index, 1)
                            : this.selectedCategories.push(id);
                    },

                    isCategorySelected(id) {
                        return this.selectedCategories.includes(String(id));
                    },
            
                    editSpare(spare) {
                        this.editingSpare = spare;
                        this.isEditing = true;
            
                        this.selectedSellers = Array.isArray(spare.sellers)
                            ? spare.sellers.map(s => String(s.id)).filter(Boolean)
                            : [];
                        this.selectedCategories = Array.isArray(spare.categories)
                            ? spare.categories.map(c => String(c.id ?? c.pivot?.machine_category_id)).filter(Boolean)
                            : [];
            
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
            
                    cancelEdit() {
                        this.editingSpare = null;
                        this.isEditing = false;
                        this.selectedSellers = [];
                        this.selectedCategories = [];
                        this.sellerDropdownOpen = false;
                        this.categoryDropdownOpen = false;
                        // Reset image preview
                        document.getElementById('imagePreview').style.display = 'none';
                        document.getElementById('editImagePreview').innerHTML = '';
                    },

                    previewImage(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = document.getElementById('imagePreview');
                                const img = document.getElementById('previewImg');
                                img.src = e.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    },

                    previewEditImage(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const preview = document.getElementById('editImagePreview');
                                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px; border-radius: 8px;">`;
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                }
            }
        </script>
            
    <!-- Success/Error Message -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <style>
        [x-cloak]{display:none !important;}
        .rotate-180 {
            transform: rotate(180deg);
        }
        .table-hover tbody tr:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover td[style*="position: sticky"] {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .list-card-col,.list-card{min-width:0}.list-header{flex-wrap:wrap}.list-header-title-row{min-width:0}.list-header-search{min-width:200px}.filter-sidebar{width:350px;max-width:100%}@media (max-width:767.98px){.filter-sidebar{width:100%!important}}@media (min-width:992px){.list-header-search{min-width:240px;max-width:360px}}
    </style>

    </div>

    <!-- CSV Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Inventory Items from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('spares.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <small class="form-text text-muted">
                                CSV format: name, description, spare_type (mechanical/electrical), quantity, image (file path or URL), sellers (comma-separated)
                            </small>
                        </div>
                        <div class="alert alert-info">
                            <strong>CSV Format:</strong>
                            <ul class="mb-0 small">
                                <li><strong>name</strong> - Required: Inventory item name</li>
                                <li><strong>description</strong> - Optional: Description</li>
                                <li><strong>spare_type</strong> - Required: 'mechanical' or 'electrical'</li>
                                <li><strong>quantity</strong> - Required: Quantity (integer, minimum 0)</li>
                                <li><strong>image</strong> - Optional: Image file path (relative to storage/app/public/spares/) or image URL</li>
                                <li><strong>sellers</strong> - Optional: Comma-separated seller names (e.g., "Seller1,Seller2")</li>
                            </ul>
                            <div class="alert alert-warning mt-2 mb-0 small">
                                <strong>Note:</strong> For images, you can use:
                                <ul class="mb-0 mt-1">
                                    <li>File path: <code>image.jpg</code> (must be in storage/app/public/spares/)</li>
                                    <li>Full URL: <code>https://example.com/image.jpg</code></li>
                                </ul>
                            </div>
                        </div>
                        @if(session('import_errors'))
                            <div class="alert alert-warning">
                                <strong>Import Errors:</strong>
                                <ul class="mb-0 small">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>

