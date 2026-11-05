@php
    $pwaName = config('app.name');
    $pwaV = \App\Models\Setting::logoVersion();
@endphp
{{-- PWA / favicon: everything below follows the app name and the uploaded system logo --}}
<link rel="manifest" href="{{ route('pwa.manifest', ['v' => $pwaV]) }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ route('pwa.icon', ['mode' => 'any', 'size' => 32, 'v' => $pwaV]) }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ route('pwa.icon', ['mode' => 'any', 'size' => 192, 'v' => $pwaV]) }}">
<link rel="apple-touch-icon" href="{{ route('pwa.icon', ['mode' => 'apple', 'size' => 180, 'v' => $pwaV]) }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ route('pwa.icon', ['mode' => 'apple', 'size' => 152, 'v' => $pwaV]) }}">
<link rel="apple-touch-icon" sizes="167x167" href="{{ route('pwa.icon', ['mode' => 'apple', 'size' => 167, 'v' => $pwaV]) }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ route('pwa.icon', ['mode' => 'apple', 'size' => 180, 'v' => $pwaV]) }}">
<meta name="application-name" content="{{ $pwaName }}">
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $pwaName }}">
<meta name="format-detection" content="telephone=no">
