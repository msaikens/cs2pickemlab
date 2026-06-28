@php
    $user = auth()->user();
    $isMarketplaceReady = $user ? $user->canUseMarketplace() : false;
    $publicLinks = config('navigation.public', []);
@endphp

<header class="public-nav-header">
    <div class="public-nav-inner">
        <a href="{{ route('home') }}" class="site-brand-logo" aria-label="CS2 PickLab Home">
        <img
            src="{{ asset('images/brand/cs2-picklab-logo-nav.png') }}"
            alt="CS2 PickLab"
            class="site-brand-logo-img"
        >
        </a>

        <nav class="public-main-nav" aria-label="Primary navigation">
            @foreach($publicLinks as $link)
                <x-navigation-link
                    :route="$link['route']"
                    :label="$link['label']"
                    :class="'public-main-nav-link ' . ($link['class'] ?? '')"
                    :active-pattern="$link['active_pattern'] ?? null"
                />

            @endforeach
                <x-navigation-link
                        route="marketplace.listings.index"
                        label="Browse Marketplace"
                        class="public-main-nav-link"
                    />
            @auth
                @if($isMarketplaceReady)
                    <x-navigation-link
                        route="marketplace.listings.index"
                        label="My Listings"
                        class="public-main-nav-link"
                    />

                    <x-navigation-link
                        route="marketplace.trade-requests.index"
                        label="Trade Requests"
                        class="public-main-nav-link"
                    />
                @else
                    <x-navigation-link
                        route="profile.steam"
                        label="Finish Marketplace Setup"
                        class="public-main-nav-link warning"
                    />
                @endif
                
                <a
                href="{{ route('account.orders.index') }}"
                class="public-main-nav-link {{ request()->routeIs('account.orders.*') ? 'active' : '' }}"
                >
                    Orders
                </a>

                @if($user?->isAdmin())
                    <x-navigation-link
                        route="users.search"
                        label="Users"
                        class="public-main-nav-link"
                    />

                    <x-navigation-link
                        route="admin.dashboard"
                        label="Admin"
                        class="public-main-nav-link admin"
                    />
                @endif
                <a
                href="{{ route('account.inbox') }}"
                class="public-main-nav-link {{ request()->routeIs('account.inbox*') ? 'active' : '' }}"
                >
                    Inbox
                @if($user->unreadInboxMessages()->exists())
                    <span class="nav-unread-dot" aria-label="Unread inbox messages"></span>
                @endif
                </a>
                <a
                    href="{{ route('account.show') }}"
                    class="public-main-nav-link account {{ request()->routeIs('account.*') ? 'active' : '' }}"
                    title="{{ $user->email }}"
                >
                    {{ $user->displayName() ?? $user->email }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="public-nav-form">
                    @csrf

                    <button type="submit" class="public-main-nav-link danger">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <x-navigation-link
                    route="login"
                    label="Sign In"
                    class="public-main-nav-link account"
                />

                <x-navigation-link
                    route="register"
                    label="Create Account"
                    class="public-main-nav-link outline"
                />
            @endguest
        </nav>
    </div>
</header>