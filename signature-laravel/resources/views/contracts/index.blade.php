<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
        <div class="mb-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-lg-auto order-lg-0">
                    <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Contracts Management</h1>
                    <p class="text-muted mb-0 small">View and manage all contracts with their status</p>
                </div>
                <div class="col-12 col-lg order-lg-1">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                        
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1 me-sm-2"></i>Create Contract
                        </a>
                       
                    </div>
                </div>
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

            <form method="GET" action="{{ route('contracts.index') }}" id="filterForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Approval Status</label>
                    <select name="approval_status" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Area</label>
                    <select name="area_id" id="filter_area_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Areas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Business Firm</label>
                    <select name="business_firm_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Business Firms</option>
                        @foreach($businessFirms as $firm)
                            <option value="{{ $firm->id }}" {{ request('business_firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-check me-2"></i>Apply
                    </button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Contracts List">Contracts List</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $contracts->total() }} Total</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                        @if(request()->hasAny(['search', 'approval_status', 'state_id', 'city_id', 'area_id', 'business_firm_id']) && (request('search') || request('approval_status') || request('state_id') || request('city_id') || request('area_id') || request('business_firm_id')))
                            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('contracts.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by contract number, name, phone, email, location..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    @if(request('approval_status'))<input type="hidden" name="approval_status" value="{{ request('approval_status') }}">@endif
                    @if(request('state_id'))<input type="hidden" name="state_id" value="{{ request('state_id') }}">@endif
                    @if(request('city_id'))<input type="hidden" name="city_id" value="{{ request('city_id') }}">@endif
                    @if(request('area_id'))<input type="hidden" name="area_id" value="{{ request('area_id') }}">@endif
                    @if(request('business_firm_id'))<input type="hidden" name="business_firm_id" value="{{ request('business_firm_id') }}">@endif
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
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer Name</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Company</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Total Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Approval Status</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr class="border-bottom">
                                <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $contracts->firstItem() + $loop->index }}</span></td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $contract->company_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-semibold" style="color: var(--primary-color);">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                </td>
                                <td class="px-2">
                                    @if($contract->approval_status === 'pending')
                                        @if($contract->customer_signature)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending Approval
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-pen me-1"></i>Awaiting Signature
                                            </span>
                                        @endif
                                    @elseif($contract->approval_status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                    @elseif($contract->approval_status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <div class="d-flex gap-2" role="group">
                                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-info" title="View Contract Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-sm btn-outline-success" title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @can('create proforma invoices')
                                            <a href="{{ route('proforma-invoices.create') }}?contract_id={{ $contract->id }}" class="btn btn-sm btn-outline-success" title="Create Proforma Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        @endcan
                                        @if(!$contract->customer_signature)
                                            <a href="{{ route('contracts.signature', $contract) }}" class="btn btn-sm btn-outline-info" title="Sign Contract">
                                                <i class="fas fa-signature"></i>
                                            </a>
                                        @endif
                                        {{-- Show Edit button only if user has 'convert contract' permission (not just 'view contract approvals') --}}
                                        @can('convert contract')
                                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary" title="Edit Contract Details">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        {{-- Show Delete button only if user has 'convert contract' permission (not just 'view contract approvals') --}}
                                        @can('convert contract')
                                            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contract?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Contract">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file-contract fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No contracts found.</p>
                                        <small class="text-muted mt-1">Create your first contract from a lead to get started</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($contracts->hasPages())
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $contracts->firstItem() }} to {{ $contracts->lastItem() }} of {{ $contracts->total() }} contracts
                    </div>
                    <div>
                        {{ $contracts->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="text-muted text-center">
                    Showing {{ $contracts->count() }} of {{ $contracts->total() }} contracts
                </div>
            </div>
        @endif
    </div>

    <script>
        function loadFilterCities(stateId) {
            const citySelect = document.getElementById('filter_city_id');
            citySelect.innerHTML = '<option value="">All Cities</option>';
            const areaSelect = document.getElementById('filter_area_id');
            areaSelect.innerHTML = '<option value="">All Areas</option>';
            
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
        function loadFilterAreas(cityId) {
            const areaSelect = document.getElementById('filter_area_id');
            areaSelect.innerHTML = '<option value="">All Areas</option>';
            if (cityId) {
                fetch(`/leads/areas/${cityId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(area => {
                            const option = document.createElement('option');
                            option.value = area.id;
                            option.textContent = area.name;
                            areaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading areas:', error));
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

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

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    </div>
</x-app-layout>
