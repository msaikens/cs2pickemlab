<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CS2 PickLab' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @if(config('monetization.adsense.enabled') && config('monetization.adsense.client'))
        <script
            async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('monetization.adsense.client') }}"
            crossorigin="anonymous">
        </script>
    @endif
</head>

<body class="bg-slate-950 text-slate-100">
    @include('layouts.partials.nav')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')
</body>
</html>