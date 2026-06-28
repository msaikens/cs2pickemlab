<!-- app/views/layouts/admin.blade.php -->

@php
    $title = $title ?? 'Admin | CS2 PickLab';
    $pageTitle = $pageTitle ?? 'Admin';
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
</head>

<body class="admin-layout-body">
    <div class="admin-layout">
        @include('layouts.partials.admin-sidebar')

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-inner">
                    <p class="admin-eyebrow">Admin</p>

                    <h1>
                        {{ $pageTitle }}
                    </h1>
                </div>
            </header>

            <section class="admin-content">
                @if(session('success'))
                    <div class="admin-alert success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="admin-alert success">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="admin-alert warning">
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-alert danger">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
</body>
</html>