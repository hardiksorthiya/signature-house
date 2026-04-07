<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $primaryColor = optional($appSettings)->primary_color ?? 'var(--primary-color)';
            $secondaryColor = optional($appSettings)->secondary_color ?? 'var(--primary-dark)';
            $logoPath = optional($appSettings)->logo ? Storage::url($appSettings->logo) : null;
            $faviconPath = optional($appSettings)->favicon ? Storage::url($appSettings->favicon) : asset('favicon.ico');
        @endphp

        <link rel="icon" href="{{ $faviconPath }}">

        <title>{{ config('app.name', 'Signature ERP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/header.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        
        <!-- Custom Theme Variables (placed after compiled CSS for override) -->
        <style>
            :root {
                --primary-color: {{ $primaryColor }};
                --primary-dark: {{ $secondaryColor }};
                --primary-light: {{ $primaryColor }};
                --primary-lighter: {{ $secondaryColor }};
                
                /* Override Bootstrap's primary color variables */
                --bs-primary: {{ $primaryColor }} !important;
                --bs-primary-rgb: 231, 67, 67 !important;
            }
            
            /* Force Bootstrap primary color override - Highest Priority */
            .btn-primary,
            .btn-primary:not(:disabled):not(.disabled):active,
            .btn-primary:not(:disabled):not(.disabled).active,
            .show > .btn-primary.dropdown-toggle,
            button.btn-primary,
            a.btn-primary,
            input.btn-primary {
                background-color: var(--primary-color) !important;
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
                border-color: var(--primary-color) !important;
                color: white !important;
            }
            
            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled):hover,
            .btn-primary:not(:disabled):not(.disabled):focus {
                background-color: var(--primary-dark) !important;
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%) !important;
                border-color: var(--primary-dark) !important;
                color: white !important;
            }
            
            /* Override Bootstrap blue primary everywhere */
            .bg-primary {
                background-color: var(--primary-color) !important;
            }
            
            .text-primary {
                color: var(--primary-color) !important;
            }
            
            .border-primary {
                border-color: var(--primary-color) !important;
            }

            /* List pages: shared table area + header flex (matches contracts index) */
            .list-card {
                min-width: 0;
            }
            .list-card .table-responsive {
                max-height: calc(100vh - 400px);
                overflow-y: auto;
            }
            .list-header {
                flex-wrap: wrap;
            }
            .list-header-title-row {
                min-width: 0;
            }
            .list-header-search {
                min-width: 200px;
            }
            .filter-sidebar {
                width: 350px;
                max-width: 100%;
            }
            @media (max-width: 767.98px) {
                .filter-sidebar {
                    width: 100% !important;
                }
            }
            @media (min-width: 992px) {
                .list-header-search {
                    min-width: 240px;
                    max-width: 360px;
                }
            }
        </style>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        
        <!-- Alpine.js -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="bg-light" x-data="{ sidebarOpen: false }" x-init="if (window.innerWidth >= 992) { sidebarOpen = true }" style="font-family: 'Roboto', sans-serif; overflow-x: hidden !important; max-width: 100vw !important;">
        <div class="d-flex vh-100 overflow-hidden" style="overflow-x: hidden !important; max-width: 100vw !important;">
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false"
                 x-cloak
                 class="d-lg-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
                 style="z-index: 1040; transition: opacity 0.3s;"></div>
            
            <!-- Mobile Sidebar -->
            <aside x-show="sidebarOpen"
                   x-cloak
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-300"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full"
                   class="d-lg-none position-fixed top-0 start-0 bg-white border-end sidebar-mobile"
                   style="z-index: 1055; overflow: auto !important;">
                <div class="sidebar-mobile-header d-flex align-items-center justify-content-between p-3 border-bottom bg-white">
                    <span class="fw-medium text-dark small">Menu</span>
                    <button type="button" @click="sidebarOpen = false" class="btn btn-link text-dark p-2 m-0" aria-label="Close menu" style="text-decoration: none;">
                        <i class="fas fa-times fs-5"></i>
                    </button>
                </div>
                <div class="sidebar-mobile-scroll">
                    @include('layouts.sidebar-content')
                </div>
            </aside>

            <!-- Desktop Sidebar -->
            <aside class="d-none d-lg-flex flex-column bg-white border-end sidebar-desktop"
                   :class="sidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'"
                   :style="sidebarOpen ? 'width: 16rem !important; min-width: 16rem !important; max-width: 16rem !important; transition: width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), min-width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), max-width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;' : 'width: 4.5rem !important; min-width: 4.5rem !important; max-width: 4.5rem !important; transition: width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), min-width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), max-width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important; overflow-x: hidden !important;'">
                @include('layouts.sidebar-content')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-grow-1 d-flex flex-column overflow-hidden">
                <!-- Header -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="flex-grow-1 overflow-auto bg-light p-3 p-lg-4">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @auth
        @cannot('view employee location')
        <!-- Live location: prompt employees to share location when site is open -->
        <div id="live-location-bar" class="position-fixed bottom-0 start-0 end-0 p-3 d-none" style="z-index: 1060; background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); backdrop-filter: blur(6px);">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3 rounded-3" style="background: color-mix(in srgb, var(--primary-color) 12%, #fff); border: 1px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">
                    <span class="text-dark small fw-medium"><i class="fas fa-map-marker-alt me-2" style="color: var(--primary-color);"></i>Share your live location so your team can see where you are.</span>
                    <div class="d-flex gap-2">
                        <button type="button" id="live-location-not-now" class="btn btn-sm btn-outline-secondary">Not now</button>
                        <button type="button" id="live-location-allow" class="btn btn-sm btn-primary"><i class="fas fa-location-crosshairs me-1"></i>Allow</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        (function() {
            var STORAGE_KEY = 'liveLocationAllowed';
            var DECLINED_KEY = 'liveLocationDeclined';
            var INTERVAL_MS = 30000;
            var updateUrl = @json(route('employee-location.update'));
            var bar = document.getElementById('live-location-bar');
            var allowBtn = document.getElementById('live-location-allow');
            var notNowBtn = document.getElementById('live-location-not-now');
            var intervalId = null;

            function sendPosition(position) {
                var csrf = document.querySelector('meta[name="csrf-token"]');
                if (!csrf) return;
                fetch(updateUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf.content, 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy != null ? Math.round(position.coords.accuracy) : null
                    })
                }).catch(function() {});
            }

            function startWatching() {
                if (intervalId) return;
                if (!navigator.geolocation) return;
                function tick() {
                    navigator.geolocation.getCurrentPosition(sendPosition, function() {}, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
                }
                tick();
                intervalId = setInterval(tick, INTERVAL_MS);
            }

            function showBar() {
                if (sessionStorage.getItem(DECLINED_KEY)) return;
                if (sessionStorage.getItem(STORAGE_KEY)) { startWatching(); return; }
                if (bar) bar.classList.remove('d-none');
            }

            if (allowBtn) {
                allowBtn.addEventListener('click', function() {
                    if (!navigator.geolocation) return;
                    if (bar) bar.classList.add('d-none');
                    navigator.geolocation.getCurrentPosition(
                        function(pos) {
                            sessionStorage.setItem(STORAGE_KEY, '1');
                            sendPosition(pos);
                            startWatching();
                        },
                        function() {
                            if (bar) bar.classList.remove('d-none');
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                });
            }
            if (notNowBtn) {
                notNowBtn.addEventListener('click', function() {
                    sessionStorage.setItem(DECLINED_KEY, '1');
                    if (bar) bar.classList.add('d-none');
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showBar);
            } else {
                showBar();
            }
        })();
        </script>
        @endcannot
        @endauth
        
        <script>
            // Debug: Check Alpine.js and sidebar state
            document.addEventListener('alpine:init', () => {
                console.log('Alpine.js initialized');
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Alpine === 'undefined') {
                    console.error('Alpine.js is not loaded');
                } else {
                    console.log('Alpine.js is loaded');
                }
            });
        </script>
    </body>
</html>

