<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Create Role</h1>
                <p class="text-muted mb-0 small">Create a new role and assign permissions</p>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1 me-sm-2"></i>Back to Team
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
            <form method="GET" action="{{ route('roles.create') }}" id="roleFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by role name..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('roleFilterForm').submit();">
                        <option value="name_asc" {{ request('sort', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('roles.create') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Split Layout: 70% Form, 30% Role List -->
    <div class="row g-4">
        <!-- Left Side: Role Form with Permissions (70%) -->
        <div class="col-xl-6 col-lg-12 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-user-tag text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Create New Role</h2>
                    </div>
                    
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <!-- Role Name -->
                        <div class="mb-4">
                            <label class="form-label fw-medium" style="color: #374151;">Role Name</label>
                            <input type="text" name="name" required
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., Editor, Viewer, etc."
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mb-4">
                            <label class="form-label fw-medium mb-3" style="color: #374151;">Permissions</label>
                            
                            @php
                                $permissionGroups = [
                                    'User Management' => ['view users', 'create users', 'edit users', 'delete users'],
                                    'Role & Permission Management' => ['view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles'],
                                    'Lead Management' => ['view leads', 'create leads', 'edit leads', 'delete leads', 'convert contract'],
                                    'Customer Management' => ['view customers', 'delete customers'],
                                    'Contract Approval' => ['view contract approvals', 'approve contracts', 'reject contracts'],
                                    'Proforma Invoice Management' => ['view proforma invoices', 'create proforma invoices', 'edit proforma invoices', 'delete proforma invoices'],
                                    'Over Invoice' => ['view over invoice', 'create over invoice', 'edit over invoice', 'delete over invoice'],
                                    'Delivery Detail' => ['view delivery detail', 'create delivery detail', 'edit delivery detail', 'delete delivery detail'],
                                    'Status' => ['view status', 'create status', 'edit status', 'delete status'],
                                    'Pre Errection' => ['view pre erection', 'create pre erection', 'edit pre erection', 'delete pre erection'],
                                    'Image Uploading' => ['view image uploading', 'create image uploading', 'edit image uploading', 'delete image uploading'],
                                    'Damage' => ['view damage', 'create damage', 'edit damage', 'delete damage'],
                                    'Serial Number' => ['view serial number', 'create serial number', 'edit serial number', 'delete serial number'],
                                    'Machine Erection' => ['view machine erection', 'create machine erection', 'edit machine erection', 'delete machine erection'],
                                    'IA Fitting' => ['view ia fitting', 'create ia fitting', 'edit ia fitting', 'delete ia fitting'],
                                    'Spare List' => ['view spare list', 'create spare list', 'edit spare list', 'delete spare list'],
                                    'Spare' => ['view spare', 'create spare', 'edit spare', 'delete spare'],
                                    'Payment' => ['view payment', 'create payment', 'edit payment', 'delete payment'],
                                    'Purchase Order' => ['view purchase order', 'create purchase order', 'edit purchase order', 'delete purchase order'],
                                    'Inventory' => ['view inventory', 'create inventory', 'edit inventory', 'delete inventory'],
                                    'Task' => ['view task', 'create task', 'edit task', 'delete task'],
                                    'Old Data' => ['view old data', 'create old data', 'edit old data', 'delete old data'],
                                    'Complain' => ['view complain', 'create complain', 'edit complain', 'delete complain'],
                                    'Reports' => ['view reports', 'export reports'],
                                    'Settings' => ['view settings', 'edit settings'],
                                ];
                            @endphp

                            <div class="overflow-y-auto overflow-x-hidden" style="max-height: calc(100vh - 450px);">
                                <div class="row g-3">
                                    @foreach($permissionGroups as $groupName => $permissionNames)
                                        <div class="col-12">
                                            <div class="border rounded p-3" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important; background: white;">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-medium mb-0" style="color: #1f2937;">{{ $groupName }}</h6>
                                                    <button type="button" 
                                                            onclick="toggleGroup('group-{{ $loop->index }}')"
                                                            class="btn btn-sm p-0">
                                                        <i class="fas fa-check-double me-1"></i>Select All
                                                    </button>
                                                </div>
                                                <div id="group-{{ $loop->index }}" class="row g-2 m-0">
                                                    @foreach($permissions as $permission)
                                                        @if(in_array($permission->name, $permissionNames))
                                                            <div class="col-md-6 col-lg-4 p-1">
                                                                <label for="permission-{{ $permission->id }}" class="d-block m-0" style="cursor: pointer;">
                                                                    <div class="permission-box p-3 rounded border position-relative" 
                                                                         style="background: color-mix(in srgb, var(--primary-color) 5%, white); border: 2px solid color-mix(in srgb, var(--primary-color) 20%, transparent) !important; transition: all 0.3s ease; min-height: 60px;"
                                                                         onmouseover="this.style.background='color-mix(in srgb, var(--primary-color) 10%, white)'; this.style.borderColor='color-mix(in srgb, var(--primary-color) 40%, transparent)'"
                                                                         onmouseout="this.style.background='color-mix(in srgb, var(--primary-color) 5%, white)'; this.style.borderColor='color-mix(in srgb, var(--primary-color) 20%, transparent)'">
                                                                        <div class="d-flex align-items-center">
                                                                            <input class="form-check-input me-3" 
                                                                                   type="checkbox" 
                                                                                   name="permissions[]" 
                                                                                   value="{{ $permission->id }}"
                                                                                   id="permission-{{ $permission->id }}"
                                                                                   {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}
                                                                                   onchange="updateBoxStyle(this)"
                                                                                   style="width: 18px; height: 18px; border-color: var(--primary-color); cursor: pointer; margin-top: 0;">
                                                                            <span class="flex-grow-1" style="font-size: 0.875rem; color: #4b5563; font-weight: 500;">
                                                                                {{ $permission->name }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @error('permissions')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assignable Roles (Roles this role can assign when creating users) -->
                        @if(in_array('create users', $permissions->pluck('name')->toArray()))
                        <div class="mb-4">
                            <label class="form-label fw-medium mb-3" style="color: #374151;">
                                <i class="fas fa-user-tag me-2"></i>Assignable Roles
                                <small class="text-muted">(Select which roles users with this role can assign when creating team members)</small>
                            </label>
                            
                            <div class="border rounded p-3" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important; background: white;">
                                <div class="row g-2">
                                    @foreach($rolesAll as $assignableRole)
                                        @if($assignableRole->name !== 'Super Admin')
                                        <div class="col-md-6 col-lg-4 p-1">
                                            <label for="assignable-role-{{ $assignableRole->id }}" class="d-block m-0" style="cursor: pointer;">
                                                <div class="permission-box p-3 rounded border position-relative" 
                                                     style="background: color-mix(in srgb, #10b981 5%, white); border: 2px solid color-mix(in srgb, #10b981 20%, transparent) !important; transition: all 0.3s ease; min-height: 60px;"
                                                     onmouseover="this.style.background='color-mix(in srgb, #10b981 10%, white)'; this.style.borderColor='color-mix(in srgb, #10b981 40%, transparent)'"
                                                     onmouseout="this.style.background='color-mix(in srgb, #10b981 5%, white)'; this.style.borderColor='color-mix(in srgb, #10b981 20%, transparent)'">
                                                    <div class="d-flex align-items-center">
                                                        <input class="form-check-input me-3" 
                                                               type="checkbox" 
                                                               name="assignable_roles[]" 
                                                               value="{{ $assignableRole->id }}"
                                                               id="assignable-role-{{ $assignableRole->id }}"
                                                               {{ old('assignable_roles') && in_array($assignableRole->id, old('assignable_roles')) ? 'checked' : '' }}
                                                               onchange="updateBoxStyle(this)"
                                                               style="width: 18px; height: 18px; border-color: #10b981; cursor: pointer; margin-top: 0;">
                                                        <span class="flex-grow-1" style="font-size: 0.875rem; color: #4b5563; font-weight: 500;">
                                                            {{ $assignableRole->name }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            @error('assignable_roles')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Note: If no roles are selected, users with this role can assign any role (except Super Admin).</small>
                        </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Role List (30%) -->
        <div class="col-xl-6 col-lg-12 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-list text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Role List">Role List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $roles->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'name_asc')))
                                    <a href="{{ route('roles.create') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('roles.create') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by role name..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                            </div>
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
                                    <th class="px-3 py-2   small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                                    <th class="px-3 py-2   small fw-semibold" style="color: var(--primary-color) !important;">Role</th>
                                    <th class="px-3 py-2   small fw-semibold" style="color: var(--primary-color) !important;">Permissions</th>
                                    <th class="px-3 py-2   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-3 py-2"><span class="fw-medium" style="color: #1f2937;">{{ $roles->firstItem() ? $roles->firstItem() + $loop->index : $loop->iteration }}</span></td>
                                        <td class="px-3 py-2">
                                            <div class="fw-medium" style="color: #1f2937; font-size: 0.9rem;">{{ $role->name }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <small class="text-muted">{{ $role->permissions->count() }} permission(s)</small>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="d-flex gap-1 justify-content-center" role="group">
                                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-info" title="Edit Role"><i class="fas fa-edit"></i></a>
                                                @if($role->name !== 'Super Admin')
                                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-user-tag fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No roles found.</p>
                                                <small class="text-muted mt-1">Create your first role to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($roles->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $roles->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <span class="fw-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <script>
        function toggleGroup(groupId) {
            const group = document.getElementById(groupId);
            const checkboxes = group.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
                updateBoxStyle(checkbox);
            });
        }
        
        function updateBoxStyle(checkbox) {
            const box = checkbox.closest('.permission-box');
            if (checkbox.checked) {
                box.style.background = 'color-mix(in srgb, var(--primary-color) 15%, white)';
                box.style.borderColor = 'var(--primary-color)';
                box.style.boxShadow = '0 2px 8px color-mix(in srgb, var(--primary-color) 20%, transparent)';
            } else {
                box.style.background = 'color-mix(in srgb, var(--primary-color) 5%, white)';
                box.style.borderColor = 'color-mix(in srgb, var(--primary-color) 20%, transparent)';
                box.style.boxShadow = 'none';
            }
        }
        
        // Initialize box styles on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
                updateBoxStyle(checkbox);
            });
            document.querySelectorAll('input[type="checkbox"][name="assignable_roles[]"]').forEach(checkbox => {
                updateBoxStyle(checkbox);
            });
        });
    </script>
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .list-card-col,.list-card{min-width:0}.list-header{flex-wrap:wrap}.list-header-title-row{min-width:0}.list-header-search{min-width:200px}.filter-sidebar{width:350px;max-width:100%}@media (max-width:767.98px){.filter-sidebar{width:100%!important}}@media (min-width:992px){.list-header-search{min-width:240px;max-width:360px}}
        .table-hover tbody tr:hover{background-color:color-mix(in srgb, var(--primary-color) 12%, #ffffff) !important;transition:all 0.2s ease;}
    </style>
    </div>
</x-app-layout>

