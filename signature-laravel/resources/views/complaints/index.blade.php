<x-app-layout>
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Complain</h1>
                <p class="text-muted mb-0 small">View and manage all complaints</p>
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

    <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-exclamation-triangle text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;">All Complaints</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $complaints->total() }} Total</span>
                    </div>
                </div>
                <form method="GET" action="{{ route('complaints.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by client, type, khata, detail..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    <input type="hidden" name="complain_type_id" value="{{ request('complain_type_id') }}">
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'complain_type_id']))
                        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Reset">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                     <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr.no</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Client</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Complain Type</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Machine Category</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Khata No. / Serial No.</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Other Detail</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Status</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Date</th>
                            <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr class="border-bottom">
                                <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $complaints->firstItem() + $loop->index }}</span></td>
                                <td class="px-2">
                                    @if($complaint->contract)
                                        <div class="fw-medium" style="color: #1f2937;">{{ $complaint->contract->company_name ?: $complaint->contract->buyer_name }}</div>
                                        <small class="text-muted">{{ $complaint->contract->contract_number }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-2">
                                    @if($complaint->complainType)
                                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">{{ $complaint->complainType->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-2"><span style="color: #374151;">{{ $complaint->machineCategory->name ?? '—' }}</span></td>
                                <td class="px-2"><span style="color: #374151;">{{ $complaint->machine_khata_number ?: '—' }}</span></td>
                                <td class="px-2"><span style="color: #6b7280;">{{ $complaint->other_detail ? Str::limit($complaint->other_detail, 40) : '—' }}</span></td>
                                <td class="px-2">
                                    @if(($complaint->status ?? 'on_going') === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">On Going</span>
                                    @endif
                                </td>
                                <td class="px-2"><small>{{ $complaint->created_at->format('d M Y') }}</small></td>
                                <td class="px-2">
                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                        @can('view complain')
                                        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                        @endcan
                                        @can('edit complain')
                                        <a href="{{ route('complaints.status', $complaint) }}" class="btn btn-sm btn-outline-success" title="Status"><i class="fas fa-tasks"></i></a>
                                        <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-info" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('complaints.assign', $complaint) }}" class="btn btn-sm btn-outline-secondary" title="Assign"><i class="fas fa-user-plus"></i></a>
                                        @endcan
                                        @can('delete complain')
                                        <form action="{{ route('complaints.destroy', $complaint) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this complaint?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-5 text-center text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.3;"></i>
                                    <p class="mb-0">No complaints found.</p>
                                    @can('create complain')
                                        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm mt-2">Create Complain</a>
                                    @endcan
                                </td>
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
</x-app-layout>
