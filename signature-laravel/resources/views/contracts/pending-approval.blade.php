<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Contract Approvals</h1>
                <p class="text-muted mb-0 small">Review and approve all contracts</p>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1 me-sm-2"></i>All Contracts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('contracts.pending-approval') }}" id="contractApprovalFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by contract #, buyer, company..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Status</label>
                    <select name="status" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Contracts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('contractApprovalFilterForm').submit();">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Contract # (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Contract # (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('contracts.pending-approval') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-check-circle text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Contract Approvals">Contract Approvals</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $contracts->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort', 'status']) && (request('search') || request('status') || (request('sort') && request('sort') != 'date_desc')))
                                    <a href="{{ route('contracts.pending-approval') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('contracts.pending-approval') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by contract #, buyer, company..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                            </div>
                            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="  overflow-y: auto; overflow-x: hidden;">
                        <table class="table table-hover mb-0 align-middle">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Contract Number</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Created By</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Total Amount</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Status</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Signed Date</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contracts as $contract)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $contracts->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $contract->contract_number }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                                            <small class="text-muted">{{ $contract->company_name ?? '' }}</small>
                                        </td>
                                        <td class="px-2">
                                            <small class="text-muted">{{ $contract->creator->name ?? 'N/A' }}</small>
                                        </td>
                                        <td class="px-2">
                                            <div class="fw-semibold" style="color: var(--primary-color);">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                        </td>
                                        <td class="px-2">
                                            @if($contract->approval_status === 'approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Approved
                                                </span>
                                            @elseif($contract->approval_status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                                </span>
                                            @elseif($contract->approval_status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-question-circle me-1"></i>Not Signed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            <small class="text-muted">{{ $contract->updated_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2">
                                                @if($contract->approval_status === 'pending' && $contract->customer_signature)
                                                    @can('approve contracts')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#approveModal{{ $contract->id }}"
                                                            style="border-radius: 6px;">
                                                        <i class="fas fa-check me-1"></i>Approve
                                                    </button>
                                                    @endcan
                                                    @can('reject contracts')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal{{ $contract->id }}"
                                                            style="border-radius: 6px;">
                                                        <i class="fas fa-times me-1"></i>Reject
                                                    </button>
                                                    @endcan
                                                @endif
                                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" title="View Full Contract Details">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-sm btn-outline-success" style="border-radius: 6px;" title="Download PDF" target="_blank">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </a>
                                                {{-- Show Edit button only if user has 'convert contract' permission (not just 'view contract approvals') --}}
                                                @can('convert contract')
                                                    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;" title="Edit Contract Details">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $contract->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Approve Contract</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('contracts.approve', $contract) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve contract <strong>{{ $contract->contract_number }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Buyer: <strong>{{ $contract->buyer_name }}</strong></label>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Total Amount: <strong>${{ number_format($contract->total_amount ?? 0, 2) }}</strong></label>
                                                        </div>
                                                        @if($contract->customer_signature)
                                                            <div class="mb-3">
                                                                <label class="form-label">Customer Signature:</label>
                                                                <div class="border rounded p-2" style="background: white;">
                                                                    <img src="{{ $contract->customer_signature }}" alt="Signature" style="max-width: 100%; height: auto; max-height: 150px;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <label class="form-label">Approval Notes (Optional)</label>
                                                            <textarea name="approval_notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check me-1"></i>Approve Contract
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $contract->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Contract</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('contracts.reject', $contract) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject contract <strong>{{ $contract->contract_number }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Buyer: <strong>{{ $contract->buyer_name }}</strong></label>
                                                        </div>
                                                        @if($contract->customer_signature)
                                                            <div class="mb-3">
                                                                <label class="form-label">Customer Signature:</label>
                                                                <div class="border rounded p-2" style="background: white;">
                                                                    <img src="{{ $contract->customer_signature }}" alt="Signature" style="max-width: 100%; height: auto; max-height: 150px;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                            <textarea name="approval_notes" class="form-control @error('approval_notes') is-invalid @enderror" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                                                            @error('approval_notes')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-times me-1"></i>Reject Contract
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-check-circle fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No contracts found.</p>
                                                <small class="text-muted mt-1">Adjust filters or add contracts</small>
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
                        <div class="d-flex justify-content-center">
                            {{ $contracts->links() }}
                        </div>
                    </div>
                @endif
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

    <style>
        .list-card-col,.list-card{min-width:0}.list-header{flex-wrap:wrap}.list-header-title-row{min-width:0}.list-header-search{min-width:200px}.filter-sidebar{width:350px;max-width:100%}@media (max-width:767.98px){.filter-sidebar{width:100%!important}}@media (min-width:992px){.list-header-search{min-width:240px;max-width:360px}}
        .table-hover tbody tr:hover{background-color:color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;transition:all 0.2s ease;}
    </style>
    </div>
</x-app-layout>
