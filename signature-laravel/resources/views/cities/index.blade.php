<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">City Management</h1>
            <p class="text-muted mb-0">Manage cities related to states</p>
        </div>
    </div>

    <!-- Filter Sidebar Overlay -->
    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <!-- Filter Sidebar -->
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('cities.index') }}" id="cityFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by city or state..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('cityFilterForm').submit();">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4" x-data="{ 
        editingCity: null, 
        isEditing: false,
        editCity(city) {
            this.editingCity = city;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingCity = null;
            this.isEditing = false;
        }
    }">
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-city'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit City' : 'Add City'"></h2>
                    </div>
                    
                    <div x-show="!isEditing">
                        <form action="{{ route('cities.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">State</label>
                                <select name="state_id" required class="form-select @error('state_id') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">City Name</label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter city name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add City
                            </button>
                        </form>
                    </div>

                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingCity">
                            <form :action="`{{ url('cities') }}/${editingCity.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: #374151;">State</label>
                                    <select name="state_id" required class="form-select" style="border-radius: 8px;" x-model="editingCity.state_id">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">City Name</label>
                                    <input type="text" name="name" required
                                           x-model="editingCity.name"
                                           class="form-control"
                                           placeholder="Enter city name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
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

        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="City List">City List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $cities->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('cities.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by city or state..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
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
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">City Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">State</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cities as $city)
                                    <tr class="border-bottom">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $cities->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $city->name }}</div>
                                        </td>
                                        <td class="px-2">
                                            <span class="badge" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.875rem; padding: 0.35rem 0.65rem;">{{ $city->state->name }}</span>
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        @click="editCity({ id: {{ $city->id }}, name: '{{ addslashes($city->name) }}', state_id: {{ $city->state_id }} })"
                                                        class="btn btn-sm btn-outline-info" 
                                                        style="border-radius: 6px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('cities.destroy', $city) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">No cities found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cities->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $cities->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    </div>
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
    <style>.list-card-col,.list-card{min-width:0}.list-header{flex-wrap:wrap}.list-header-title-row{min-width:0}.list-header-search{min-width:200px}.filter-sidebar{width:350px;max-width:100%}@media (max-width:767.98px){.filter-sidebar{width:100%!important}}@media (min-width:992px){.list-header-search{min-width:240px;max-width:360px}}</style>
</x-app-layout>




