<div>
    <!-- Profile Image Display -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-3" style="color: #374151;">
            <i class="fas fa-user-circle me-2"></i>Profile Image
        </label>
        
        <div class="text-center mb-3">
            @if($user->profile_image)
                <img src="{{ Storage::url($user->profile_image) }}" 
                     alt="Profile Image" 
                     class="rounded-circle border shadow-sm" 
                     style="width: 150px; height: 150px; object-fit: cover; border: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;">
            @else
                <div class="rounded-circle border d-flex align-items-center justify-content-center mx-auto" 
                     style="width: 150px; height: 150px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); border: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;">
                    <i class="fas fa-user text-white" style="font-size: 4rem;"></i>
                </div>
            @endif
        </div>
    </div>

    <!-- Signature Display -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-3" style="color: #374151;">
            <i class="fas fa-signature me-2"></i>Signature
        </label>
        
        <div class="text-center mb-3" style="background: #f9fafb; border-radius: 8px; padding: 1rem; min-height: 120px; display: flex; align-items: center; justify-content: center;">
            @if($user->signature)
                <img src="{{ Storage::url($user->signature) }}" 
                     alt="Signature" 
                     class="img-fluid" 
                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
            @else
                <div class="text-center">
                    <i class="fas fa-signature text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-2 mb-0 small">No signature uploaded</p>
                </div>
            @endif
        </div>
    </div>

    <div class="alert alert-info mb-0" style="border-radius: 8px;">
        <i class="fas fa-info-circle me-2"></i>
        <small>Upload or update your profile image and signature using the "Profile Information" form on the left.</small>
    </div>
</div>
