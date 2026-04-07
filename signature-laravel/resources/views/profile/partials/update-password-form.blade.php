<form method="post" action="{{ route('password.update') }}" class="mt-0">
        @csrf
        @method('put')

    <div class="mb-3">
        <label class="form-label fw-medium" style="color: #374151;">Current Password</label>
        <input type="password" 
               name="current_password" 
               id="update_password_current_password"
               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
               placeholder="Enter current password"
               style="border-radius: 8px; border: 1px solid #e5e7eb;">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        </div>

    <div class="mb-3">
        <label class="form-label fw-medium" style="color: #374151;">New Password</label>
        <input type="password" 
               name="password" 
               id="update_password_password"
               class="form-control @error('password', 'updatePassword') is-invalid @enderror"
               placeholder="Enter new password"
               style="border-radius: 8px; border: 1px solid #e5e7eb;">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        </div>

    <div class="mb-4">
        <label class="form-label fw-medium" style="color: #374151;">Confirm Password</label>
        <input type="password" 
               name="password_confirmation" 
               id="update_password_password_confirmation"
               class="form-control"
               placeholder="Confirm new password"
               style="border-radius: 8px; border: 1px solid #e5e7eb;">
        </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Update Password
        </button>
        </div>
    </form>
