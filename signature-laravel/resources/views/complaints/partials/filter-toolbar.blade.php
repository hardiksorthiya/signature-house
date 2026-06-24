@php
    $complaintFilterParams = request()->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']);
    $hasComplaintFilters = collect($complaintFilterParams)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();
    $filterResetUrl = route($filterRoute, $filterResetParams ?? []);
@endphp
<div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
    <button type="button"
            @click="filterSidebarOpen = !filterSidebarOpen"
            class="btn border-0 d-flex align-items-center justify-content-center p-0"
            style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);"
            title="Filter">
        <i class="fas fa-filter"></i>
    </button>
    @if($hasComplaintFilters)
        <a href="{{ $filterResetUrl }}"
           class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
           style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;"
           title="Clear Filters">
            <i class="fas fa-times"></i>
        </a>
    @endif
</div>
