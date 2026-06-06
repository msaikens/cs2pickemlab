<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CS2 PickLab' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100">
    @include('layouts.partials.nav')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('layouts.partials.footer')
</body>
</html>