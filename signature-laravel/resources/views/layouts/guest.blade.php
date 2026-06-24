<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $logoPath = optional($appSettings)->logo ? Storage::url($appSettings->logo) : null;
            $faviconPath = optional($appSettings)->favicon ? Storage::url($appSettings->favicon) : $defaultAppIcon;
        @endphp

        <link rel="icon" type="image/png" href="{{ $faviconPath }}">
        @include('layouts.pwa-meta')

        <title>{{ config('app.name', 'Signature In House') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <style>
            :root {
                --login-primary: #e74343;
                --login-primary-dark: #9f2323;
                --login-primary-light: #fc9e9e;
            }
            .guest-page {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
                background: linear-gradient(160deg, #fafafa 0%, #f8f6f6 35%, #f5f2f2 70%, #f0ebeb 100%);
                position: relative;
                overflow: hidden;
            }
            .guest-page::before {
                content: '';
                position: absolute;
                inset: 0;
                background: 
                    radial-gradient(ellipse 80% 50% at 20% 40%, rgba(231, 67, 67, 0.07) 0%, transparent 50%),
                    radial-gradient(ellipse 60% 40% at 80% 60%, rgba(231, 67, 67, 0.05) 0%, transparent 50%);
                pointer-events: none;
            }
            .guest-card {
                width: 100%;
                max-width: 420px;
                background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--login-primary) 5%, #ffffff) 100%);
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 10px 20px -5px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
                padding: 0;
                position: relative;
                z-index: 1;
                overflow: hidden;
            }
            .guest-card-header {
                padding: 1.25rem 2rem 0.875rem;
                border-bottom: 2px solid var(--login-primary);
                background: transparent;
            }
            .guest-logo-wrap {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .guest-logo-wrap a {
                display: inline-flex;
                flex-direction: column;
                align-items: center;
                gap: 0.25rem;
                text-decoration: none;
            }
            .guest-logo-wrap img {
                height: 3.5rem;
                width: auto;
            }
            .guest-logo-wrap svg {
                height: 3.5rem;
                width: auto;
                color: var(--login-primary);
            }
            .guest-brand-name {
                font-size: 1.25rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                color: var(--login-primary);
            }
            .guest-brand-tagline {
                font-size: 0.7rem;
                font-weight: 500;
                letter-spacing: 0.08em;
                color: #374151;
                text-transform: uppercase;
            }
            .guest-card-body {
                padding: 1.25rem 2rem 1.5rem;
            }
            .guest-input {
                width: 100%;
                padding: 0.625rem 1rem;
                font-size: 0.9375rem;
                line-height: 1.5;
                color: #1f2937;
                background-color: #f3f4f6;
                border: 1px solid #d1d5db;
                border-radius: 10px;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .guest-input::placeholder {
                color: #9ca3af;
            }
            .guest-input:focus {
                outline: none;
                border-color: var(--login-primary);
                box-shadow: 0 0 0 3px rgba(231, 67, 67, 0.15);
            }
            .guest-btn-login {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 0.75rem 1.25rem;
                font-size: 0.9375rem;
                font-weight: 500;
                color: #fff;
                background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-dark) 100%);
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(231, 67, 67, 0.35);
                cursor: pointer;
                transition: all 0.25s ease;
            }
            .guest-btn-login:hover {
                background: linear-gradient(135deg, var(--login-primary-dark) 0%, var(--login-primary) 100%);
                box-shadow: 0 6px 16px rgba(231, 67, 67, 0.4);
                transform: translateY(-1px);
            }
            .guest-btn-login:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(231, 67, 67, 0.3);
            }
            .guest-alert {
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                padding: 0.875rem 1rem;
                border-radius: 10px;
                margin-bottom: 1rem;
                border: 1px solid;
            }
            .guest-alert-error {
                background-color: #fef2f2;
                border-color: #fecaca;
                color: #991b1b;
            }
            .guest-alert-icon {
                flex-shrink: 0;
                width: 1.25rem;
                height: 1.25rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                font-weight: 700;
                line-height: 1;
                color: var(--login-primary);
            }
            .guest-alert-title {
                font-weight: 500;
                font-size: 0.875rem;
                margin: 0 0 0.125rem 0;
                color: #991b1b;
            }
            .guest-alert-message {
                font-size: 0.8125rem;
                margin: 0;
                color: #b91c1c;
                line-height: 1.4;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased" style="font-family: 'Roboto', sans-serif;">
        <div class="guest-page">
            <div class="guest-card">
                <div class="guest-card-header">
                    <div class="guest-logo-wrap">
                        <a href="/">
                            @if(!empty($logoPath))
                                <img src="{{ $logoPath }}" alt="{{ config('app.name') }}">
                            @else
                                <x-application-logo class="w-14 h-14 fill-current" style="color: var(--login-primary);" />
                            @endif
                            {{-- <span class="guest-brand-name">{{ config('app.name', 'SIGNATURE') }}</span>
                            <span class="guest-brand-tagline">Textile Machines</span> --}}
                        </a>
                    </div>
                </div>
                <div class="guest-card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
