<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Port of Destination Management</h1>
                <p class="text-muted mb-0 small">Manage ports of destination</p>
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
            <form method="GET" action="{{ route('port-of-destinations.index') }}" id="portFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by port name or code..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('portFilterForm').submit();">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('port-of-destinations.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Split Layout: 30% Form, 70% Table -->
    <div class="row g-4" x-data="{ 
        editingPort: null, 
        isEditing: false,
        editPort(port) {
            this.editingPort = port;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingPort = null;
            this.isEditing = false;
        }
    }">
        <!-- Left Side: Add/Edit Port Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-anchor'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Port' : 'Add Port'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        <form action="{{ route('port-of-destinations.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Port Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter port name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Port Code</label>
                                <input type="text" name="code"
                                       value="{{ old('code') }}"
                                       class="form-control @error('code') is-invalid @enderror"
                                       placeholder="Enter port code"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                <textarea name="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Enter description"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add Port
                            </button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingPort">
                            <form :action="`{{ url('admin/port-of-destinations') }}/${editingPort.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Port Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required
                                           x-model="editingPort.name"
                                           class="form-control"
                                           placeholder="Enter port name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Port Code</label>
                                    <input type="text" name="code"
                                           x-model="editingPort.code"
                                           class="form-control"
                                           placeholder="Enter port code"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                    <textarea name="description" rows="3"
                                              x-model="editingPort.description"
                                              class="form-control"
                                              placeholder="Enter description"
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-medium">
                                        <i class="fas fa-save me-2"></i>Update Port
                                    </button>
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary py-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Ports Table (70%) -->
        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Ports of Destination">Ports of Destination</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $ports->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('port-of-destinations.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('port-of-destinations.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by port name or code..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
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
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Port Name</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Port Code</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Description</th>
                                    <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ports as $port)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $ports->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            <div class="fw-medium" style="color: #1f2937;">{{ $port->name }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div style="color: #6b7280;">{{ $port->code ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div style="color: #6b7280;">{{ Str::limit($port->description ?? 'N/A', 50) }}</div>
                                        </td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button @click="editPort(@js($port))" class="action-btn action-btn-edit" title="Edit">
                                                    <i class="fas fa-edit" style="font-size: 14px;"></i>
                                                </button>
                                                <form action="{{ route('port-of-destinations.destroy', $port->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this port?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                        <i class="fas fa-trash" style="font-size: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-anchor fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No ports of destination found.</p>
                                                <small class="text-muted mt-1">Add your first port to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ports->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $ports->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <div class="d-flex align-items-center"><i class="fas fa-check-circle me-2 fs-5"></i><span class="fw-medium">{{ session('success') }}</span></div>
        </div>
        <style>@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}</style>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
            <div class="d-flex align-items-center"><i class="fas fa-times-circle me-2"></i><span>{{ session('error') }}</span></div>
        </div>
    @endif

    <style>
        .list-card-col,.list-card{min-width:0}.list-header{flex-wrap:wrap}.list-header-title-row{min-width:0}.list-header-search{min-width:200px}.filter-sidebar{width:350px;max-width:100%}@media (max-width:767.98px){.filter-sidebar{width:100%!important}}@media (min-width:992px){.list-header-search{min-width:240px;max-width:360px}}
        .table-hover tbody tr:hover{background-color:color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;transition:all 0.2s ease;}
        .action-btn{width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;background:transparent;border-radius:6px;transition:all 0.2s ease;text-decoration:none;border:1px solid}.action-btn:hover{transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,0.1)}.action-btn-edit{border-color:#800020;color:#800020}.action-btn-edit:hover{background-color:#800020;color:white}.action-btn-delete{border-color:#dc2626;color:#dc2626}.action-btn-delete:hover{background-color:#dc2626;color:white}
    </style>
    </div>
</x-app-layout>

<style>
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-btn-edit {
        border-color: #800020;
        color: #800020;
    }
    .action-btn-edit:hover {
        background-color: #800020;
        color: white;
    }
    .action-btn-delete {
        border-color: #dc2626;
        color: #dc2626;
    }
    .action-btn-delete:hover {
        background-color: #dc2626;
        color: white;
    }
</style>
