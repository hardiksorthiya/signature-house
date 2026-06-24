<link rel="manifest" href="{{ route('mobile-app.manifest') }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Signature In House') }}">
@php
    $pwaIcon = optional($appSettings ?? null)->favicon
        ? Storage::url($appSettings->favicon)
        : $defaultAppIcon;
@endphp
<link rel="apple-touch-icon" href="{{ $pwaIcon }}">
