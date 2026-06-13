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

    @stack('styles')
</head>

<body class="admin-layout-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="CS2 PickLab Admin Dashboard">
                <span class="admin-brand-title">
                    CS2 PickLab
                </span>

                <span class="admin-brand-subtitle">
                    Admin Panel
                </span>
            </a>

            <nav class="admin-nav" aria-label="Admin navigation">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">
                    Dashboard
                </a>

                <a href="{{ route('admin.teams.index') }}" class="admin-nav-link">
                    Teams
                </a>

                <a href="{{ route('admin.players.index') }}" class="admin-nav-link">
                    Players
                </a>

                <a href="{{ route('admin.events.index') }}" class="admin-nav-link">
                    Events
                </a>

                <a href="{{ route('admin.matches.index') }}" class="admin-nav-link">
                    Matches
                </a>

                <a href="{{ route('admin.predictions.index') }}" class="admin-nav-link">
                    Predictions
                </a>

                <a href="{{ route('admin.pickem.index') }}" class="admin-nav-link">
                    Pick&#8217;em
                </a>

                <a href="{{ route('admin.products.index') }}" class="admin-nav-link">
                    Products
                </a>

                <a href="{{ route('admin.orders.index') }}" class="admin-nav-link">
                    Orders
                </a>

                <a href="{{ route('admin.marketplace.listings') }}" class="admin-nav-link">
                    Marketplace Listings
                </a>

                <a href="{{ route('admin.marketplace.trade-requests') }}" class="admin-nav-link">
                    Marketplace Trades
                </a>
            </nav>
        </aside>

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