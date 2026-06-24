@php
    $viewLabels = [
        'list' => 'All Complaints',
        'recurring' => 'Recurring Complaints (Source)',
        'machine' => 'Machine Wise',
        'date' => 'Date Wise',
        'area' => 'Area Wise',
        'master' => 'Engineer Assign & Status',
    ];
@endphp
<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Complaints Report</h1>
            <p class="text-muted mb-0 small">Filter and analyze complaints — including engineer assignments with complaint status</p>
        </div>
    </div>

    @include('reports.partials.complaint-report-filters', [
        'reportTitle' => $viewLabels[$view] ?? 'Complaints Report',
        'reportTotal' => $totalCount,
    ])

    @if($view === 'list')
        <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
            <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Complaints <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $complaints->total() }}</span></h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                            <tr>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Client</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Area</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Complain Type</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Machine Category</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Khata No.</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Status</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($complaints as $complaint)
                                @include('reports.partials.complaint-row', ['complaint' => $complaint])
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-5 text-center text-muted">No complaints found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($complaints->hasPages())
                <div class="card-footer border-0 bg-transparent py-2">{{ $complaints->links() }}</div>
            @endif
        </div>
    @else
        @if(($groupsPaginator?->total() ?? 0) === 0)
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body px-4 py-5 text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.3;"></i>
                    <p class="mb-0">
                        @if($view === 'master')
                            No assigned complaints found for the selected filters.
                        @else
                            No complaint groups found for the selected filters.
                        @endif
                    </p>
                </div>
            </div>
        @else
            @foreach($groups as $group)
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
                    <div class="card-header border-0 py-3 px-3 px-md-4" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent); background: transparent;">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div>
                                <h3 class="h6 fw-semibold mb-0" style="color: #1f2937;">{{ $group['label'] }}</h3>
                                @if(!empty($group['subtitle']))
                                    <small class="text-muted">{{ $group['subtitle'] }}</small>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @if($view === 'master')
                                    <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color);">
                                        {{ $group['count'] }} Total
                                    </span>
                                    <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">
                                        {{ $group['active_count'] ?? 0 }} On Going
                                    </span>
                                    <span class="badge bg-success">
                                        {{ $group['completed_count'] ?? 0 }} Completed
                                    </span>
                                @else
                                    <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color);">
                                        {{ $group['count'] }} {{ Str::plural('complaint', $group['count']) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                                    <tr>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Client</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Area</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Complain Type</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Machine Category</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Khata No.</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Status</th>
                                        <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['complaints'] as $complaint)
                                        @include('reports.partials.complaint-row', ['complaint' => $complaint])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
            @if($groupsPaginator->hasPages())
                <div class="d-flex justify-content-center mb-4">{{ $groupsPaginator->links() }}</div>
            @endif
        @endif
    @endif
</x-app-layout>
