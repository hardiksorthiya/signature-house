<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto order-lg-0">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Leads Management</h1>
                    <p class="text-muted mb-0 small">View and manage all generated leads</p>
                </div>
                @can('create leads')
                <div class="col-12 col-lg order-lg-1">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                        <a href="{{ route('leads.import.template') }}" class="btn btn-outline-info">
                            <i class="fas fa-download me-1 me-sm-2"></i><span class="d-none d-sm-inline">Download Excel Template</span><span class="d-inline d-sm-none">Template</span>
                        </a>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importLeadsModal">
                            <i class="fas fa-file-excel me-1 me-sm-2"></i>Import Excel
                        </button>
                        <a href="{{ route('leads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1 me-sm-2"></i>Create New Lead
                        </a>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        <!-- Filter Sidebar Overlay -->
        <div x-show="filterSidebarOpen" 
             x-cloak
             @click="filterSidebarOpen = false"
             class="position-fixed top-0 start-0 w-100 h-100 bg-dark"
             style="opacity: 0.5; z-index: 1040;"></div>

        <!-- Filter Sidebar -->
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

            <form method="GET" action="{{ route('leads.index') }}" id="filterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control" 
                           placeholder="Search by name, phone, business, location..." 
                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Lead Type</label>
                    <select name="type" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Types</option>
                        <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="old" {{ request('type') == 'old' ? 'selected' : '' }}>Old</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Status</label>
                    <select name="status_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">State</label>
                    <select name="state_id" id="filter_state_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="loadFilterCities(this.value);">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">City</label>
                    <select name="city_id" id="filter_city_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Cities</option>
                        @if(request('state_id'))
                            @php
                                $filterCities = \App\Models\City::where('state_id', request('state_id'))->orderBy('name')->get();
                            @endphp
                            @foreach($filterCities as $city)
                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Business</label>
                    <select name="business_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" {{ request('business_id') == $business->id ? 'selected' : '' }}>{{ $business->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Brand</label>
                    <select name="brand_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Planning Month</label>
                    <select name="planning_month" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)request('planning_month') === $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Planning Year</label>
                    <select name="planning_year" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Years</option>
                        @php
                            $currentYear = now()->year;
                            $planningYears = range($currentYear - 2, $currentYear + 5);
                        @endphp
                        @foreach($planningYears as $year)
                            <option value="{{ $year }}" {{ (int)request('planning_year') === $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-check me-2"></i>Apply
                    </button>
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

        <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <!-- Mobile/Tablet: (title + filter) then search. Desktop lg+: (title + filter) left, search right -->
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="All Leads">All Leads</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $leads->total() }} Total</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" 
                                @click="filterSidebarOpen = !filterSidebarOpen"
                                class="btn border-0 d-flex align-items-center justify-content-center p-0" 
                                style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter">
                            <i class="fas fa-filter"></i>
                        </button>
                        @if(request()->hasAny(['search', 'type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id', 'planning_month', 'planning_year']))
                            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('leads.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="form-control form-control-sm" 
                               placeholder="Search by name, phone, business, location..." 
                               style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    @if(request()->hasAny(['type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id', 'planning_month', 'planning_year']))
                        @foreach(request()->only(['type', 'status_id', 'state_id', 'city_id', 'business_id', 'brand_id', 'planning_month', 'planning_year']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                    @endif
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                     <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Sr.no</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Name</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Phone</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Location</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Machine Category</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Quantity</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Lead Date</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Lead Time</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Planning Timeline</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Lead By</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Status</th>
                            <th class="p-2 small fw-semibold"  style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr class="border-bottom">
                                <td class="px-2">
                                    <span class="fw-medium" style="color: #1f2937;">{{ $leads->firstItem() + $loop->index }}</span>
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $lead->name }}</div>
                                </td>
                                <td class="px-2">
                                    <span class="text-muted">{{ $lead->phone_number }}</span>
                                </td>
                                <td class="px-2">
                                    <span class="text-muted">{{ $lead->area->name }}, {{ $lead->city->name }}, {{ $lead->state->name }}</span>
                                </td>
                                <td class="px-2">
                                    @if($lead->machineCategories->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($lead->machineCategories->take(2) as $category)
                                                <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.75rem;">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                            @if($lead->machineCategories->count() > 2)
                                                <span class="badge bg-secondary" style="font-size: 0.75rem;">+{{ $lead->machineCategories->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No categories</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <span class="fw-medium" style="color: #1f2937;">{{ $lead->quantity ?? '-' }}</span>
                                </td>
                                <td class="px-2">
                                    <span class="text-muted">{{ $lead->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="px-2">
                                    <span class="text-muted">{{ $lead->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-2">
                                    <span class="text-muted">
                                        @if($lead->planning_month && $lead->planning_year)
                                            {{ date('M Y', mktime(0, 0, 0, $lead->planning_month, 1, $lead->planning_year)) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                                <td class="px-2">
                                    @php
                                        $creator = $lead->creator;
                                    @endphp
                                    @if($lead->created_by && $creator)
                                        @if($lead->created_by == auth()->id())
                                            <span class="badge" style="background-color: color-mix(in srgb, #10b981 15%, #ffffff); color: #059669; font-size: 0.875rem; padding: 0.35rem 0.65rem;">
                                                You
                                            </span>
                                        @else
                                            <span class="fw-medium" style="color: #1f2937;">
                                                {{ $creator->name }}
                                            </span>
                                        @endif
                                    @elseif($lead->created_by)
                                        @if($lead->created_by == auth()->id())
                                            <span class="badge" style="background-color: color-mix(in srgb, #10b981 15%, #ffffff); color: #059669; font-size: 0.875rem; padding: 0.35rem 0.65rem;">
                                                You
                                            </span>
                                        @else
                                            <span class="text-muted">User #{{ $lead->created_by }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <span class="badge bg-info text-white">{{ $lead->status->name }}</span>
                                </td>
                                <td class="px-2">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('edit leads')
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @if($lead->contract)
                                            <a href="{{ route('contracts.edit', $lead->contract) }}" class="btn btn-sm btn-outline-success" title="View Contract">
                                                <i class="fas fa-file-contract"></i>
                                            </a>
                                        @else
                                            @can('convert contract')
                                            <a href="{{ route('leads.convert-to-contract', $lead) }}" class="btn btn-sm btn-outline-success" title="Convert to Contract">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                            @endcan
                                        @endif
                                        @can('delete leads')
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-friends fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No leads found.</p>
                                        <span class="text-muted mt-1 d-block">Create your first lead to get started</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} leads
                    </div>
                    <div>
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="text-muted text-center">
                    Showing {{ $leads->count() }} of {{ $leads->total() }} leads
                </div>
            </div>
        @endif
    </div>

    <script>
        function loadFilterCities(stateId) {
            const citySelect = document.getElementById('filter_city_id');
            citySelect.innerHTML = '<option value="">All Cities</option>';
            
            if (stateId) {
                fetch(`/leads/cities/${stateId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading cities:', error));
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Import Excel Modal --}}
    @can('create leads')
    <div class="modal fade" id="importLeadsModal" tabindex="-1" aria-labelledby="importLeadsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importLeadsModalLabel"><i class="fas fa-file-excel me-2 text-success"></i>Import Leads from Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="leads_excel_file" class="form-label fw-medium">Select Excel File (.xlsx or .xls)</label>
                            <input type="file" class="form-control" id="leads_excel_file" name="file" accept=".xlsx,.xls" required>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="alert alert-light border mb-0">
                            <strong>Required columns:</strong> Name, Phone (or Phone Number)<br>
                            <strong>Optional columns:</strong> Type, State, City, Area, Business, Status, Brand, Quantity<br>
                            <a href="{{ route('leads.import.template') }}">Download template</a> for the correct format. Duplicate phone numbers will be skipped.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

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
    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show m-4" role="alert">
            <strong>Import notes:</strong>
            <ul class="mb-0 mt-1">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    </div>
</x-app-layout>



