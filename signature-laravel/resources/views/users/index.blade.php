<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Team Management</h1>
                <p class="text-muted mb-0 small">Manage team members and roles</p>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="fas fa-user-tag me-1 me-sm-2"></i>Create Role
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
            <form method="GET" action="{{ route('users.index') }}" id="userFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or phone..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sort By</label>
                    <select name="sort" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('userFilterForm').submit();">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                        <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Split Layout: 30% Form, 70% Table -->
    <div class="row g-4" x-data="{ 
        editingUser: null, 
        isEditing: false,
        editUser(user) {
            this.editingUser = user;
            this.isEditing = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editingUser = null;
            this.isEditing = false;
        }
    }">
        <!-- Left Side: Add/Edit Team Member Form (30%) -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-user-edit' : 'fa-user-plus'"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Team Member' : 'Add Team Member'"></h2>
                    </div>
                    
                    <!-- Add Form -->
                    <div x-show="!isEditing">
                        @can('create users')
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Name</label>
                                <input type="text" name="name" required
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter full name"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium" style="color: #374151;">Phone Number</label>
                                <input type="text" name="phone" required
                                       value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Enter phone number"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" x-data="{ showPass: false }">
                                <label class="form-label fw-medium" style="color: #374151;">Password</label>
                                <div class="input-group">
                                    <input :type="showPass ? 'text' : 'password'" name="password" required
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Enter password"
                                           style="border-radius: 8px 0 0 8px; border: 1px solid #e5e7eb;">
                                    <button type="button" class="btn btn-outline-secondary" @click="showPass = !showPass" tabindex="-1" aria-label="Show password" style="border: 1px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0;">
                                        <i class="fas" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" x-data="{ showPassConfirm: false }">
                                <label class="form-label fw-medium" style="color: #374151;">Confirm Password</label>
                                <div class="input-group">
                                    <input :type="showPassConfirm ? 'text' : 'password'" name="password_confirmation" required
                                           class="form-control"
                                           placeholder="Confirm password"
                                           style="border-radius: 8px 0 0 8px; border: 1px solid #e5e7eb;">
                                    <button type="button" class="btn btn-outline-secondary" @click="showPassConfirm = !showPassConfirm" tabindex="-1" aria-label="Show password" style="border: 1px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0;">
                                        <i class="fas" :class="showPassConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium" style="color: #374151;">Role</label>
                                <select name="role" required
                                        class="form-select @error('role') is-invalid @enderror"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                <i class="fas fa-plus me-2"></i>Add Team Member
                            </button>
                        </form>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>You don't have permission to create users.
                        </div>
                        @endcan
                    </div>

                    <!-- Edit Form -->
                    <div x-show="isEditing" x-cloak>
                        @can('edit users')
                        <template x-if="editingUser">
                            <form :action="`{{ url('users') }}/${editingUser.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Name</label>
                                    <input type="text" name="name" required
                                           x-model="editingUser.name"
                                           class="form-control"
                                           placeholder="Enter full name"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Phone Number</label>
                                    <input type="text" name="phone" required
                                           x-model="editingUser.phone"
                                           class="form-control"
                                           placeholder="Enter phone number"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <div class="mb-3" x-data="{ showPass: false }">
                                    <label class="form-label fw-medium" style="color: #374151;">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                                    <div class="input-group">
                                        <input :type="showPass ? 'text' : 'password'" name="password"
                                               class="form-control"
                                               placeholder="Enter new password (optional)"
                                               style="border-radius: 8px 0 0 8px; border: 1px solid #e5e7eb;">
                                        <button type="button" class="btn btn-outline-secondary" @click="showPass = !showPass" tabindex="-1" aria-label="Show password" style="border: 1px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0;">
                                            <i class="fas" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3" x-data="{ showPassConfirm: false }">
                                    <label class="form-label fw-medium" style="color: #374151;">Confirm Password</label>
                                    <div class="input-group">
                                        <input :type="showPassConfirm ? 'text' : 'password'" name="password_confirmation"
                                               class="form-control"
                                               placeholder="Confirm new password"
                                               style="border-radius: 8px 0 0 8px; border: 1px solid #e5e7eb;">
                                        <button type="button" class="btn btn-outline-secondary" @click="showPassConfirm = !showPassConfirm" tabindex="-1" aria-label="Show password" style="border: 1px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0;">
                                            <i class="fas" :class="showPassConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Role</label>
                                    <select name="role" required
                                            x-model="editingUser.current_role"
                                            class="form-select"
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </form>
                        </template>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>You don't have permission to edit users.
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Team List Table (70%) -->
        <div class="col-lg-8 col-md-12 list-card-col" style="min-width: 0;">
            <div class="card shadow-sm border-0 h-100 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                            <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-users text-white small"></i>
                                </div>
                                <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="Team List">Team List</h2>
                                <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $users->total() }} Total</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                                <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                                @if(request()->hasAny(['search', 'sort']) && (request('search') || (request('sort') && request('sort') != 'date_desc')))
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name or phone..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
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
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">User</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Phone</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Role</th>
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Status</th>
                                    @hasrole('Admin|Super Admin')
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Created By</th>
                                    @endhasrole
                                    <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $users->firstItem() + $loop->index }}</span></td>
                                        <td class="px-2">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 500; font-size: 16px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="color: #1f2937;">{{ $user->name }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2">
                                            <div style="color: #4b5563;">{{ $user->phone ?? '-' }}</div>
                                        </td>
                                        <td class="px-2">
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="badge me-1" style="
                                                        @if($role->name == 'Super Admin') background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;
                                                        @elseif($role->name == 'Admin') background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white;
                                                        @elseif($role->name == 'Manager') background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;
                                                        @elseif($role->name == 'Staff') background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;
                                                        @else background: #6b7280; color: white;
                                                        @endif
                                                        padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                    ">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary" style="padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px;">No Role</span>
                                            @endif
                                        </td>
                                        <td class="px-2">
                                            @if($user->is_active ?? true)
                                                <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px;">Active</span>
                                            @else
                                                <span class="badge" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; padding: 0.4em 0.8em; font-size: 0.75rem; border-radius: 6px;">Deactive</span>
                                            @endif
                                            @hasrole('Admin|Super Admin')
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ ($user->is_active ?? true) ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ ($user->is_active ?? true) ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas {{ ($user->is_active ?? true) ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endhasrole
                                        </td>
                                        @hasrole('Admin|Super Admin')
                                        <td class="px-2">
                                            @php
                                                $creator = $user->creator;
                                            @endphp
                                            @if($user->created_by && $creator)
                                                <span class="fw-medium" style="color: #1f2937;">{{ $creator->name }}</span>
                                            @elseif($user->created_by)
                                                <small class="text-muted">User #{{ $user->created_by }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        @endhasrole
                                        <td class="px-2">
                                            <div class="d-flex gap-2" role="group">
                                                @can('edit users')
                                                <button type="button" 
                                                        @click="editUser({
                                                            id: {{ $user->id }},
                                                            name: '{{ addslashes($user->name) }}',
                                                            phone: '{{ addslashes($user->phone ?? '') }}',
                                                            current_role: '{{ $user->roles->first()?->name ?? '' }}'
                                                        })"
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('delete users')
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasAnyRole(['Admin', 'Super Admin']) ? '7' : '6' }}" class="text-center text-muted py-5">
                                            <i class="fas fa-users fa-2x mb-3 d-block" style="color: #d1d5db;"></i>
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
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
        </style>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    </div>
</x-app-layout>



