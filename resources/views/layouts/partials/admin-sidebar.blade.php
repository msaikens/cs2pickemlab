@php
    $adminLinks = config('navigation.admin');

    if (empty($adminLinks)) {
        $adminLinks = [
            ['label' => 'Front End', 'route' => 'home'],
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],

            ['heading' => 'Moderation'],
            ['label' => 'Crackdown', 'route' => 'admin.crackdown.index'],

            ['heading' => 'CS2 Content'],
            ['label' => 'Teams', 'route' => 'admin.teams.index'],
            ['label' => 'Players', 'route' => 'admin.players.index'],
            ['label' => 'Events', 'route' => 'admin.events.index'],
            ['label' => 'Matches', 'route' => 'admin.matches.index'],
            ['label' => 'Predictions', 'route' => 'admin.predictions.index'],
            ['label' => 'Pick’em', 'route' => 'admin.pickem.index'],
            ['label' => 'GRID Imports', 'route' => 'admin.grid.index'],

            ['heading' => 'Commerce'],
            ['label' => 'Products', 'route' => 'admin.products.index'],
            ['label' => 'Orders', 'route' => 'admin.orders.index'],
            ['label' => 'Marketplace Listings', 'route' => 'admin.marketplace.listings'],
            ['label' => 'Marketplace Trades', 'route' => 'admin.marketplace.trade-requests'],
            ['label' => 'Wallet Terms', 'route' => 'admin.wallet-terms.acceptances'],

            ['heading' => 'System'],
            ['label' => 'Content Gates', 'route' => 'admin.content-gates.index'],
        ];
    }
@endphp

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
        @foreach($adminLinks as $link)
            @if(isset($link['heading']))
                <div class="admin-nav-heading">
                    {{ $link['heading'] }}
                </div>
            @elseif(isset($link['route']) && \Illuminate\Support\Facades\Route::has($link['route']))
                <a
                    href="{{ route($link['route']) }}"
                    class="admin-nav-link {{ request()->routeIs($link['route']) ? 'active' : '' }}"
                    @if(request()->routeIs($link['route'])) aria-current="page" @endif
                >
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</aside>