<aside class="admin-sidebar">
    <div class="px-4 py-5">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-black tracking-tight text-white">
            CS2 <span class="text-cyan-400">PickLab</span>
        </a>
        <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-500">Control Panel</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link">Dashboard</a>

        <div class="admin-nav-heading">CS2 Content</div>

        <a href="{{ route('admin.teams.index') }}" class="admin-nav-link">Teams</a>
        <a href="{{ route('admin.players.index') }}" class="admin-nav-link">Players</a>
        <a href="{{ route('admin.events.index') }}" class="admin-nav-link">Events</a>
        <a href="{{ route('admin.matches.index') }}" class="admin-nav-link">Matches</a>
        <a href="{{ route('admin.predictions.index') }}" class="admin-nav-link">Predictions</a>
        <a href="{{ route('admin.pickem.index') }}" class="admin-nav-link">Pick’em</a>

        <div class="admin-nav-heading">Shop</div>

        <a href="{{ route('admin.products.index') }}" class="admin-nav-link">Products</a>
        <a href="{{ route('admin.orders.index') }}" class="admin-nav-link">Orders</a>
        <a href="{{ route('admin.marketplace.listings') }}" class="admin-nav-link">Marketplace Listings</a>
        <a href="{{ route('admin.marketplace.trade-requests') }}" class="admin-nav-link">Marketplace Trades</a>
    </nav>
</aside>
