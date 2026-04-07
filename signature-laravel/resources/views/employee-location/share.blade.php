<x-app-layout>
    <div class="mb-4">
        <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Share my location</h1>
        <p class="text-muted mb-0 small">Allow your browser to access your location, then click the button below. Your latest location will be visible to admins on the Employee Location map.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 420px;">
        <div class="card-body text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-map-marker-alt text-white fa-2x"></i>
            </div>
            <p class="text-muted mb-4">Your location is only sent when you click the button. It is not tracked continuously.</p>
            <button type="button" id="share-location-btn" class="btn btn-primary btn-lg">
                <i class="fas fa-location-crosshairs me-2"></i>Share my location now
            </button>
            <div id="share-status" class="mt-4 small"></div>
        </div>
    </div>

    @can('view employee location')
    <div class="mt-4">
        <a href="{{ route('employee-location.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-map me-1"></i>View Employee Location map</a>
    </div>
    @endcan

    <script>
    document.getElementById('share-location-btn').addEventListener('click', function() {
        const btn = this;
        const status = document.getElementById('share-status');
        btn.disabled = true;
        status.innerHTML = '<span class="text-muted">Getting location…</span>';

        if (!navigator.geolocation) {
            status.innerHTML = '<span class="text-danger">Geolocation is not supported by your browser.</span>';
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy != null ? Math.round(position.coords.accuracy) : null;

                fetch('{{ route("employee-location.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        accuracy: accuracy,
                    }),
                })
                    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data }; }); })
                    .then(function({ ok, data }) {
                        if (ok && data.success) {
                            status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Location shared successfully. Admins can see it on the map.</span>';
                        } else {
                            status.innerHTML = '<span class="text-danger">Failed to save location. Try again.</span>';
                        }
                    })
                    .catch(function() {
                        status.innerHTML = '<span class="text-danger">Request failed. Try again.</span>';
                    })
                    .finally(function() {
                        btn.disabled = false;
                    });
            },
            function(err) {
                let msg = 'Could not get your location. ';
                if (err.code === 1) msg += 'You denied access or it was blocked.';
                else if (err.code === 2) msg += 'Position unavailable.';
                else if (err.code === 3) msg += 'Request timed out.';
                else msg += err.message || 'Unknown error.';
                status.innerHTML = '<span class="text-danger">' + msg + '</span>';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });
    </script>
</x-app-layout>
