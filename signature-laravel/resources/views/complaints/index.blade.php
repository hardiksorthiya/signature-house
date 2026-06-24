@php
    $complaintTab = $tab ?? request('tab', 'active');
    $complaintTab = in_array($complaintTab, ['active', 'completed'], true) ? $complaintTab : 'active';
    $complaintFilterQuery = request()->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']);
@endphp
<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">All Complaints</h1>
                    <p class="text-muted mb-0 small">View all active and completed complaints by month</p>
                </div>
                @can('create complain')
                <div class="col-12 col-lg-auto">
                    <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1 me-sm-2"></i>Create Complain
                    </a>
                </div>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @include('complaints.partials.filter-sidebar', [
            'filterRoute' => 'complaints.index',
            'filterResetParams' => ['tab' => $complaintTab],
            'filterHidden' => ['tab' => $complaintTab],
        ])

        <div class="card shadow-sm border-0 list-card mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-list text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;">
                                {{ $complaintTab === 'completed' ? 'All Completed Complaints' : 'All Active Complaints' }}
                            </h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $totalCount }} Total</span>
                        </div>
                        @include('complaints.partials.filter-toolbar', [
                            'filterRoute' => 'complaints.index',
                            'filterResetParams' => ['tab' => $complaintTab],
                        ])
                    </div>
                    <form method="GET" action="{{ route('complaints.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                        <div class="flex-grow-1" style="min-width: 0;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by client, type, khata, detail..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                        </div>
                        <input type="hidden" name="tab" value="{{ $complaintTab }}">
                        @foreach(request()->only(['area_id', 'machine_category_id', 'complain_type_id']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="px-3 px-md-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-inline-flex flex-wrap gap-2 p-1 rounded" style="background: color-mix(in srgb, var(--primary-color) 8%, #ffffff);">
                        <a href="{{ route('complaints.index', array_merge($complaintFilterQuery, ['tab' => 'active'])) }}"
                           class="btn btn-sm px-3 {{ $complaintTab === 'active' ? 'btn-primary' : 'btn-light border-0' }}"
                           style="border-radius: 8px;">
                            <i class="fas fa-clock me-1"></i>Active Complain
                        </a>
                        <a href="{{ route('complaints.index', array_merge($complaintFilterQuery, ['tab' => 'completed'])) }}"
                           class="btn btn-sm px-3 {{ $complaintTab === 'completed' ? 'btn-primary' : 'btn-light border-0' }}"
                           style="border-radius: 8px;">
                            <i class="fas fa-check-circle me-1"></i>Completed Complain
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($totalCount === 0)
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body px-4 py-5 text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.3;"></i>
                    <p class="mb-0">No {{ $complaintTab === 'completed' ? 'completed' : 'active' }} complaints found.</p>
                    @can('create complain')
                        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm mt-2">Create Complain</a>
                    @endcan
                </div>
            </div>
        @else
            @foreach($complaintsByMonth as $month)
                <div class="card shadow-sm border-0 list-card mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                    <div class="card-header border-0 py-3 px-3 px-md-4" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent); background: transparent;">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: color-mix(in srgb, var(--primary-color) 12%, #fff); color: var(--primary-color);">
                                    <i class="fas fa-calendar-alt small"></i>
                                </span>
                                <h3 class="h6 fw-semibold mb-0" style="color: #1f2937;">{{ $month['label'] }}</h3>
                            </div>
                            <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color);">
                                {{ $month['complaints']->count() }} {{ Str::plural('complaint', $month['complaints']->count()) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @include('complaints.partials.table', [
                            'complaints' => $month['complaints'],
                            'dateColumnHeader' => 'Date',
                            'dateColumn' => 'date',
                            'emptyRowMessage' => 'No complaints in this month.',
                        ])
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <style>
        .filter-sidebar { width: 350px; max-width: 100%; }
        @media (max-width: 767.98px) { .filter-sidebar { width: 100% !important; } }
    </style>
</x-app-layout>
