@php
    $complaintFilterParams = request()->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']);
    $hasComplaintFilters = collect($complaintFilterParams)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();
    $filterResetUrl = route($filterRoute, $filterResetParams ?? []);
@endphp

<div x-show="filterSidebarOpen"
     x-cloak
     @click="filterSidebarOpen = false"
     class="position-fixed top-0 start-0 w-100 h-100 bg-dark"
     style="opacity: 0.5; z-index: 1040;"></div>

<div x-show="filterSidebarOpen"
     x-cloak
     class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar"
     style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;"
     @click.stop>
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-semibold mb-0" style="color: #1f2937;">
                <i class="fas fa-filter me-2 text-primary"></i>Filters
            </h5>
            <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="GET" action="{{ route($filterRoute) }}" id="complaintFilterForm">
            @foreach($filterHidden ?? [] as $hiddenName => $hiddenValue)
                <input type="hidden" name="{{ $hiddenName }}" value="{{ $hiddenValue }}">
            @endforeach

            <div class="mb-3">
                <label class="form-label fw-medium" style="color: #374151;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control"
                       placeholder="Search by client, type, khata, detail..."
                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium" style="color: #374151;">Area</label>
                <select name="area_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    <option value="">All Areas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ (string) request('area_id') === (string) $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium" style="color: #374151;">Machine Category</label>
                <select name="machine_category_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    <option value="">All Machine Categories</option>
                    @foreach($machineCategories as $category)
                        <option value="{{ $category->id }}" {{ (string) request('machine_category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium" style="color: #374151;">Complain Type</label>
                <select name="complain_type_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    <option value="">All Complain Types</option>
                    @foreach($complainTypes as $type)
                        <option value="{{ $type->id }}" {{ (string) request('complain_type_id') === (string) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-check me-2"></i>Apply
                </button>
                <a href="{{ $filterResetUrl }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>
