@php
    $title = $title ?? 'CS2 PickLab';
    $pageTitle = $pageTitle ?? null;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('favicons/favicon.ico') }}" sizes="any">
    @stack('styles')

    @if(config('monetization.adsense.enabled') && config('monetization.adsense.client'))
        <script
            async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('monetization.adsense.client') }}"
            crossorigin="anonymous">
        </script>
    @endif
</head>

<body class="public-layout-body">
    <div class="public-layout">
        @include('layouts.partials.nav')

        <main class="public-main">
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>

    @stack('scripts')
</body>
</html>