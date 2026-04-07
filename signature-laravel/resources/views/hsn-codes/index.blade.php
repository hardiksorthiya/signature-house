<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('hsn-codes.index') }}" id="hsnCodeFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('hsnCodeFilterForm').submit();">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('hsn-codes.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4" x-data="hsnCodeApp()">

        <!-- LEFT FORM -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-hashtag'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit HSN Code' : 'Add HSN Code'"></h2>
                    </div>

                    <!-- ADD FORM -->
                    <div x-show="!isEditing">
                        <form action="{{ route('hsn-codes.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Name</label>
                                <input type="text" name="name" class="form-control" required
                                       placeholder="Enter name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Categories <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
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

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add HSN Code
                            </button>
                        </form>
                    </div>

                    <!-- EDIT FORM -->
                    <div x-show="isEditing" x-cloak>
                        <form :action="`{{ url('hsn-codes') }}/${editingHsnCode.id}`" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Name</label>
                                <input type="text" name="name" x-model="editingHsnCode.name" class="form-control" required
                                       placeholder="Enter name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Categories <small class="text-muted">(Multiple Select)</small></label>
                                <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                    <button type="button" 
                                            @click="categoryDropdownOpen = !categoryDropdownOpen"
                                            class="form-control text-start d-flex justify-content-between align-items-center"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                        <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                        <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
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

                            <div class="d-flex gap-2">
                                <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-save me-2"></i>Update
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT TABLE -->
        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="HSN Code List">HSN Code List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $hsnCodes->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('hsn-codes.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('hsn-codes.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
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
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Categories</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hsnCodes as $hsnCode)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $hsnCodes->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 500; font-size: 16px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);">
                                                    {{ strtoupper(substr($hsnCode->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="color: #1f2937; font-size: 0.95rem;">{{ $hsnCode->name }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $hsnCode->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2">
                                            @if($hsnCode->machineCategories->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($hsnCode->machineCategories as $category)
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
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt me-2 text-muted" style="font-size: 0.75rem;"></i>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ $hsnCode->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editHsnCode({
                                                            id: {{ $hsnCode->id }},
                                                            name: '{{ addslashes($hsnCode->name) }}',
                                                            categories: @js($hsnCode->machineCategories)
                                                        })"
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="Edit HSN Code"
>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('hsn-codes.destroy', $hsnCode) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this HSN Code?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete HSN Code"
>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-hashtag fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No HSN Codes found.</p>
                                                <small class="text-muted mt-1">Add your first HSN Code to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($hsnCodes->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $hsnCodes->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function hsnCodeApp() {
                return {
                    editingHsnCode: null,
                    isEditing: false,
                    selectedCategories: [],
                    categoryDropdownOpen: false,
            
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
            
                    editHsnCode(hsnCode) {
                        this.editingHsnCode = hsnCode;
                        this.isEditing = true;
            
                        this.selectedCategories = Array.isArray(hsnCode.categories)
                            ? hsnCode.categories.map(c => String(c.id)).filter(Boolean)
                            : [];
            
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
            
                    cancelEdit() {
                        this.editingHsnCode = null;
                        this.isEditing = false;
                        this.selectedCategories = [];
                        this.categoryDropdownOpen = false;
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
        .rotate-180 {
            transform: rotate(180deg);
        }
        .table-hover tbody tr:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    </div>
    </div>
</x-app-layout>



