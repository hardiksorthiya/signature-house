<x-app-layout>
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Complain Type</h1>
                <p class="text-muted mb-0 small">Manage complain types used in Complain module</p>
            </div>
        </div>
    </div>

    <div class="row g-4" x-data="{
        editingType: null,
        isEditing: false,
        editType(type) {
            this.editingType = type;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingType = null;
            this.isEditing = false;
        }
    }">
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-exclamation-circle'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Complain Type' : 'Add Complain Type'"></h2>
                    </div>

                    <div x-show="!isEditing">
                        <form action="{{ route('complain-types.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="e.g. Electric, Mechanical"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add Complain Type
                            </button>
                        </form>
                    </div>

                    <div x-show="isEditing" x-cloak>
                        <template x-if="editingType">
                            <form :action="`{{ url('admin/complain-types') }}/${editingType.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Type Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required x-model="editingType.name"
                                           class="form-control" placeholder="e.g. Electric"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-medium">
                                        <i class="fas fa-save me-2"></i>Update
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

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-list text-white small"></i>
                            </div>
                            <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;">Complain Types</h2>
                            <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $complainTypes->total() }} Total</span>
                        </div>
                        <form method="GET" action="{{ route('complain-types.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search type name..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px; min-width: 160px;">
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px;" title="Search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                             <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                                <tr>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Type Name</th>
                                    <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complainTypes as $type)
                                    <tr class="border-bottom">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $complainTypes->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2"><div class="fw-medium" style="color: #1f2937;">{{ $type->name }}</div></td>
                                        <td class="px-2">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button type="button" @click="editType(@js($type))" class="btn btn-sm btn-outline-info" title="Edit"><i class="fas fa-edit"></i></button>
                                                <form action="{{ route('complain-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this complain type?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="fas fa-exclamation-circle fa-2x mb-2" style="opacity: 0.3;"></i>
                                            <p class="mb-0">No complain types found. Add one using the form.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($complainTypes->hasPages())
                    <div class="card-footer bg-transparent border-top py-2">{{ $complainTypes->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center"><i class="fas fa-check-circle me-2"></i><span class="fw-medium">{{ session('success') }}</span></div>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg bg-danger text-white px-4 py-3" style="z-index: 1050;">
            <div class="d-flex align-items-center"><i class="fas fa-times-circle me-2"></i><span>{{ session('error') }}</span></div>
        </div>
    @endif
</x-app-layout>
