@php
    $exportParams = array_merge(
        request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search', 'view', 'area_id', 'machine_category_id', 'complain_type_id', 'status', 'engineer_id']),
        ['format' => 'excel']
    );
    $exportPdfParams = array_merge($exportParams, ['format' => 'pdf']);
    $viewQuery = request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search', 'area_id', 'machine_category_id', 'complain_type_id', 'status', 'engineer_id']);
    $currentView = request('view', 'list');
@endphp

@include('reports.partials.filters', [
    'formAction' => route('reports.complaints'),
    'resetUrl' => route('reports.complaints'),
    'exportExcelUrl' => route('reports.complaints.export', $exportParams),
    'exportPdfUrl' => route('reports.complaints.export', $exportPdfParams),
    'creators' => $creators,
    'sortOptions' => [
        ['value' => 'created_at', 'label' => 'Date'],
        ['value' => 'status', 'label' => 'Status'],
    ],
    'defaultSort' => 'created_at',
    'reportTitle' => $reportTitle ?? 'Complaints Report',
    'reportTotal' => $reportTotal ?? null,
    'complaintExtraFilters' => true,
    'areas' => $areas ?? collect(),
    'machineCategories' => $machineCategories ?? collect(),
    'complainTypes' => $complainTypes ?? collect(),
    'engineers' => $engineers ?? collect(),
])

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
    <div class="card-body py-3 px-3 px-md-4">
        <div class="d-inline-flex flex-wrap gap-2 p-1 rounded" style="background: color-mix(in srgb, var(--primary-color) 8%, #ffffff);">
            @foreach([
                'list' => ['icon' => 'fa-list', 'label' => 'All Complaints'],
                'recurring' => ['icon' => 'fa-redo', 'label' => 'Recurring (Source)'],
                'machine' => ['icon' => 'fa-cogs', 'label' => 'Machine Wise'],
                'date' => ['icon' => 'fa-calendar-day', 'label' => 'Date Wise'],
                'area' => ['icon' => 'fa-map-marker-alt', 'label' => 'Area Wise'],
                'master' => ['icon' => 'fa-user-cog', 'label' => 'Engineer Assign & Status'],
            ] as $viewKey => $viewMeta)
                <a href="{{ route('reports.complaints', array_merge($viewQuery, ['view' => $viewKey])) }}"
                   class="btn btn-sm px-3 {{ $currentView === $viewKey ? 'btn-primary' : 'btn-light border-0' }}"
                   style="border-radius: 8px;">
                    <i class="fas {{ $viewMeta['icon'] }} me-1"></i>{{ $viewMeta['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
