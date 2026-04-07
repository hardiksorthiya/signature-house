<div>
    <p class="text-muted mb-4">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
    </p>

    <button type="button" 
            class="btn btn-danger"
            data-bs-toggle="modal" 
            data-bs-target="#deleteAccountModal">
        <i class="fas fa-trash-alt me-2"></i>Delete Account
    </button>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="deleteAccountModalLabel" style="color: #1f2937;">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

                    <div class="mb-3">
                        <label class="form-label fw-medium" style="color: #374151;">Password</label>
                        <input type="password" 
                    name="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Enter your password to confirm"
                               required
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
            </div>
        </form>
        </div>
    </div>
</div>
