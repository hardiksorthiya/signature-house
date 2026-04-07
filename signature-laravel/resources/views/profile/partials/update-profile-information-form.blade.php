<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label class="form-label fw-medium" style="color: #374151;">Name</label>
        <input type="text" name="name" required
               value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Enter your full name"
               style="border-radius: 8px; border: 1px solid #e5e7eb;">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="form-label fw-medium" style="color: #374151;">Phone Number</label>
        <input type="text" name="phone" required
               value="{{ old('phone', $user->phone) }}"
               class="form-control @error('phone') is-invalid @enderror"
               placeholder="Enter your phone number"
               style="border-radius: 8px; border: 1px solid #e5e7eb;">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Profile Image -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-3" style="color: #374151;">
            <i class="fas fa-user-circle me-2"></i>Profile Image
        </label>
        
        <div class="text-center mb-3">
            @if($user->profile_image)
                <img src="{{ Storage::url($user->profile_image) }}" 
                     alt="Profile Image" 
                     id="profileImagePreview"
                     class="rounded-circle border shadow-sm" 
                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;">
            @else
                <div id="profileImagePreview" class="rounded-circle border d-flex align-items-center justify-content-center mx-auto" 
                     style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); border: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;">
                    <i class="fas fa-user text-white" style="font-size: 3rem;"></i>
                </div>
            @endif
        </div>

        <input type="file" 
               name="profile_image" 
               id="profile_image"
               accept="image/*"
               class="form-control @error('profile_image') is-invalid @enderror"
               style="border-radius: 8px; border: 1px solid #e5e7eb;"
               onchange="previewImage(this, 'profileImagePreview')">
        @error('profile_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
    </div>

    <!-- Signature -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-3" style="color: #374151;">
            <i class="fas fa-signature me-2"></i>Signature
        </label>
        
        <div class="text-center mb-3" style="background: #f9fafb; border-radius: 8px; padding: 1rem; min-height: 100px; display: flex; align-items: center; justify-content: center;">
            @if($user->signature)
                <img src="{{ Storage::url($user->signature) }}" 
                     alt="Signature" 
                     id="signaturePreview"
                     class="img-fluid" 
                     style="max-height: 100px; max-width: 100%; object-fit: contain;">
            @else
                <div id="signaturePreview" class="text-center">
                    <i class="fas fa-signature text-muted" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-2 mb-0 small">No signature uploaded</p>
                </div>
            @endif
        </div>

        <input type="file" 
               name="signature" 
               id="signature"
               accept="image/*"
               class="form-control @error('signature') is-invalid @enderror"
               style="border-radius: 8px; border: 1px solid #e5e7eb;"
               onchange="previewImage(this, 'signaturePreview')">
        @error('signature')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewId === 'profileImagePreview') {
                // For profile image, replace the div with img
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded-circle border shadow-sm';
                    img.style.cssText = 'width: 120px; height: 120px; object-fit: cover; border: 3px solid color-mix(in srgb, var(--primary-color) 30%, transparent) !important;';
                    preview.parentNode.replaceChild(img, preview);
                    img.id = previewId;
                } else {
                    preview.src = e.target.result;
                }
            } else {
                // For signature
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid';
                    img.style.cssText = 'max-height: 100px; max-width: 100%; object-fit: contain;';
                    preview.parentNode.replaceChild(img, preview);
                    img.id = previewId;
                } else {
                    preview.src = e.target.result;
                }
            }
        };
        reader.readAsDataURL(file);
    }
}
</script>

