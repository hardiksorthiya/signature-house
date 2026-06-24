<x-guest-layout>
    <div class="mb-3 text-center">
        <h1 class="text-xl font-bold tracking-tight" style="color: #1f2937;">Mobile App</h1>
        <p class="mt-1 text-sm" style="color: #6b7280;">Download for Android or add a shortcut on iPhone / iPad</p>
    </div>

    {{-- Android --}}
    <div class="mb-3 p-3 rounded-3 border" style="border-color: color-mix(in srgb, var(--login-primary) 25%, #e5e7eb); background: color-mix(in srgb, var(--login-primary) 4%, #fff);">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fab fa-android fa-lg" style="color: #3ddc84;"></i>
            <span class="fw-semibold" style="color: #1f2937;">Android</span>
        </div>
        <p class="small text-muted mb-3">Download and install the app on your phone. Allow <strong>Install unknown apps</strong> if Android asks.</p>

        @if($androidAvailable)
            <a href="{{ route('mobile-app.android') }}"
               class="guest-btn-login text-decoration-none d-block text-center mb-2">
                <i class="fas fa-download me-2"></i>Download for Android
            </a>
            <p class="small text-muted mb-0 text-break">
                Direct link:
                <a href="{{ url('/downloads/signature-in-house.apk') }}" style="color: var(--login-primary);">
                    {{ url('/downloads/signature-in-house.apk') }}
                </a>
            </p>
        @else
            <div class="small p-2 rounded" style="background: #fff; border: 1px dashed #d1d5db; color: #6b7280;">
                Android app is not available yet. Admin can build it with:
                <code>cd signature-mobile && npm run build:android</code>
            </div>
        @endif
    </div>

    {{-- iOS shortcut --}}
    <div class="mb-3 p-3 rounded-3 border" style="border-color: #e5e7eb; background: #fafafa;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fab fa-apple fa-lg" style="color: #1f2937;"></i>
            <span class="fw-semibold" style="color: #1f2937;">iPhone / iPad</span>
        </div>
        <p class="small text-muted mb-3">Apple does not allow direct APK-style download. Add a home screen shortcut in <strong>Safari</strong>:</p>
        <ol class="small text-muted ps-3 mb-3" style="line-height: 1.65;">
            <li>Open this page in <strong>Safari</strong> (not Chrome).</li>
            <li>Tap <strong>Share</strong> <i class="fas fa-share-square"></i></li>
            <li>Tap <strong>Add to Home Screen</strong> → <strong>Add</strong></li>
            <li>Open the new icon — it opens Signature In House like an app</li>
        </ol>
        <a href="{{ route('login') }}"
           class="btn w-100 py-2 fw-medium text-decoration-none d-flex align-items-center justify-content-center gap-2"
           style="border-radius: 10px; border: 1px solid #e5e7eb; background: #fff; color: #1f2937;">
            <i class="fas fa-external-link-alt"></i> Open login page (for shortcut)
        </a>

        @if($iosTestFlightUrl)
            <a href="{{ $iosTestFlightUrl }}" target="_blank" rel="noopener"
               class="btn w-100 py-2 mt-2 fw-medium text-decoration-none d-flex align-items-center justify-content-center gap-2"
               style="border-radius: 10px; border: 1px solid #e5e7eb; background: #fff; color: #1f2937;">
                <i class="fab fa-apple"></i> Install via TestFlight (optional)
            </a>
        @endif
    </div>

    <div class="text-center">
        <a href="{{ route('login') }}" class="small fw-semibold text-decoration-none" style="color: var(--login-primary);">
            <i class="fas fa-arrow-left me-1"></i>Back to login
        </a>
    </div>
</x-guest-layout>
