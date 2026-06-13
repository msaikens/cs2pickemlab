<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="CS2 PickLab Admin Dashboard">
        <span class="admin-brand-title">
            CS2 PickLab
        </span>

        <span class="admin-brand-subtitle">
            Control Panel
        </span>
    </a>

    <nav class="admin-nav" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">
            Dashboard
        </a>

        <div class="admin-nav-heading">
            CS2 Content
        </div>

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

        <div class="admin-nav-heading">
            Shop
        </div>

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