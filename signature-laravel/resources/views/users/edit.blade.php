<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Team Member</h1>
            <p class="text-muted mb-0">Update team member information</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i>Back to Team
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12 mx-auto">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-user-edit text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Edit Team Member: {{ $user->name }}</h2>
                    </div>
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #374151;">Name</label>
                            <input type="text" name="name" required
                                   value="{{ old('name', $user->name) }}"
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
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Enter phone number"
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" x-data="{ showPass: false }">
                            <label class="form-label fw-medium" style="color: #374151;">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                            <div class="input-group">
                                <input :type="showPass ? 'text' : 'password'" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter new password (optional)"
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
                                    class="form-select @error('role') is-invalid @enderror"
                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" 
                                        {{ (old('role', $user->roles->first()?->name) == $role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Team Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-success text-white px-4 py-3 rounded shadow-lg" style="z-index: 1050; background: linear-gradient(45deg, #22c55e, #4ade80) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
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
</x-app-layout>



