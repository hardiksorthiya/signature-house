<x-app-layout>
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Dashboard</a>
        <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Employee Location</h1>
        <p class="text-muted mb-0 small">View your team's last shared locations on the map. Employees can share their location from the "Share my location" page.</p>
    </div>

    @if(empty($mapsKey))
        <div class="alert alert-warning shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Google Maps API key not set.</strong> Add <code>GOOGLE_MAPS_API_KEY</code> to your <code>.env</code> file and reload to show the map.
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div id="employee-map" style="height: 70vh; min-height: 400px; background: #e5e7eb;"></div>
        <div class="card-footer border-0 bg-light py-2 px-3 small text-muted">
            <span id="map-legend">Loading locations…</span>
            <a href="{{ route('employee-location.share') }}" class="ms-3">Share my location</a>
        </div>
    </div>

    @if(!empty($mapsKey))
    <script>
    (function() {
        const mapsKey = @json($mapsKey);
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(mapsKey) + '&callback=initEmployeeMap';
        script.async = true;
        script.defer = true;
        window.initEmployeeMap = function() {
            const mapEl = document.getElementById('employee-map');
            const map = new google.maps.Map(mapEl, {
                center: { lat: 20.5937, lng: 78.9629 },
                zoom: 5,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
            });

            const markers = [];
            const infoWindows = [];

            function fetchLocations() {
                fetch('{{ route('employee-location.locations') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        markers.forEach(m => m.setMap(null));
                        infoWindows.forEach(w => w.close());
                        markers.length = 0;
                        infoWindows.length = 0;

                        const locations = data.locations || [];
                        document.getElementById('map-legend').textContent =
                            locations.length === 0
                                ? 'No locations shared yet. Ask employees to use "Share my location".'
                                : locations.length + ' employee(s) with location.';

                        if (locations.length === 0) return;

                        const bounds = new google.maps.LatLngBounds();
                        locations.forEach(function(loc) {
                            const pos = { lat: loc.latitude, lng: loc.longitude };
                            const marker = new google.maps.Marker({
                                position: pos,
                                map: map,
                                title: loc.user_name,
                            });
                            markers.push(marker);
                            bounds.extend(pos);

                            const recorded = loc.recorded_at ? new Date(loc.recorded_at).toLocaleString() : '—';
                            const info = new google.maps.InfoWindow({
                                content: '<div class="p-2"><strong>' + (loc.user_name || '—') + '</strong><br><small>' + (loc.user_email || '') + '</small><br><small>Last updated: ' + recorded + '</small></div>',
                            });
                            infoWindows.push(info);
                            marker.addListener('click', function() {
                                infoWindows.forEach(w => w.close());
                                info.open(map, marker);
                            });
                        });
                        if (locations.length > 1) map.fitBounds(bounds);
                        else if (locations.length === 1) map.setCenter(bounds.getCenter());
                    })
                    .catch(function() {
                        document.getElementById('map-legend').textContent = 'Failed to load locations.';
                    });
            }

            fetchLocations();
            setInterval(fetchLocations, 15000);
        };
        document.head.appendChild(script);
    })();
    </script>
    @else
    <script>
        document.getElementById('map-legend').textContent = 'Set GOOGLE_MAPS_API_KEY in .env to load the map.';
    </script>
    @endif
</x-app-layout>
