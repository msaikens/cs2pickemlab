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

    @stack('styles')
</head>

<body class="public-layout-body">
    <div class="public-layout">
        <header class="public-site-header">
            <div class="public-site-header-inner">
                <a href="{{ route('home') }}" class="public-brand" aria-label="CS2 PickLab Home">
                    <span class="public-brand-title">
                        CS2 PickLab
                    </span>

                    <span class="public-brand-subtitle">
                        Pick&#8217;em &middot; Matches &middot; Teams &middot; Shop
                    </span>
                </a>

                <nav class="public-nav" aria-label="Primary navigation">
                    <a href="{{ route('home') }}" class="public-nav-link">
                        Home
                    </a>

                    <a href="{{ route('matches.index') }}" class="public-nav-link">
                        Matches
                    </a>

                    <a href="{{ route('teams.index') }}" class="public-nav-link">
                        Teams
                    </a>

                    <a href="{{ route('pickem.index') }}" class="public-nav-link">
                        Pick&#8217;em
                    </a>

                    <a href="{{ route('shop.index') }}" class="public-nav-link">
                        Shop
                    </a>

                    @auth
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="public-nav-link admin">
                                Admin
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="public-main">
            @yield('content')
        </main>

        <footer class="public-site-footer">
            <div class="public-site-footer-inner">
                <p>
                    &copy; {{ date('Y') }} CS2 PickLab
                </p>

                <p>
                    CS2 Pick&#8217;em tools, match tracking, and recommendations.
                </p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>