@php
    $user = auth()->user();
    $isMarketplaceReady = $user ? $user->canUseMarketplace() : false;
@endphp

<header class="public-nav-header">
    <div class="public-nav-inner">
        <a href="{{ route('home') }}" class="public-nav-brand" aria-label="CS2 PickLab Home">
            CS2 <span>PickLab</span>
        </a>

        <nav class="public-main-nav" aria-label="Primary navigation">
            <a href="{{ route('marketplace.index') }}" class="public-main-nav-link featured">
                Marketplace
            </a>

            <a href="{{ route('matches.index') }}" class="public-main-nav-link">
                Matches
            </a>

            <a href="{{ route('pickem.index') }}" class="public-main-nav-link">
                Pick&#8217;em
            </a>

            <a href="{{ route('teams.index') }}" class="public-main-nav-link">
                Teams
            </a>

            <a href="{{ route('shop.index') }}" class="public-main-nav-link featured">
                Shop
            </a>

            @auth
                @if ($isMarketplaceReady)
                    @if (Route::has('marketplace.listings.create'))
                        <a href="{{ route('marketplace.listings.index') }}" class="public-main-nav-link">
                            My Listings
                        </a>

                        <a href="{{ route('marketplace.listings.create') }}" class="public-main-nav-link outline">
                            Sell Skins
                        </a>
                    @endif

                    @if (Route::has('marketplace.trade-requests.index'))
                        <a href="{{ route('marketplace.trade-requests.index') }}" class="public-main-nav-link">
                            Trade Requests
                        </a>
                    @endif
                @else
                    @if (Route::has('profile.steam'))
                        <a href="{{ route('profile.steam') }}" class="public-main-nav-link warning">
                            Finish Marketplace Setup
                        </a>
                    @endif
                @endif

                @if (Route::has('users.search'))
                    <a href="{{ route('users.search') }}" class="public-main-nav-link">
                        Users
                    </a>
                @endif

                <a
                    href="{{ route('account.show') }}"
                    class="public-main-nav-link account"
                    title="{{ auth()->user()->email }}"
                >
                    {{ auth()->user()->displayName() ?? auth()->user()->email }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="public-nav-form">
                    @csrf

                    <button type="submit" class="public-main-nav-link danger">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="public-main-nav-link account">
                    Sign In
                </a>

                <a href="{{ route('register') }}" class="public-main-nav-link outline">
                    Create Account
                </a>
            @endguest
        </nav>
    </div>
</header>