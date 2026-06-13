@extends('layouts.public', [
    'title' => 'Sell CS2 Skins | CS2 PickLab',
    'pageTitle' => 'Sell Skins',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-sell.css') }}">
@endpush

@section('content')
<section class="sell-skins-page">
    <div class="sell-skins-header">
        <div>
            <p class="sell-kicker">Marketplace</p>
            <h1>Sell CS2 Skins</h1>
            <p>Select one item from your synced Steam inventory and create a clean marketplace listing.</p>
        </div>

        <div class="sell-header-actions">
            <a href="{{ route('marketplace.listings.index') }}" class="marketplace-button secondary">
                My Listings
            </a>

            <form method="POST" action="{{ route('profile.steam.inventory.sync') }}">
                @csrf
                <button type="submit" class="marketplace-button secondary">
                    Re-Sync Inventory
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="sell-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="sell-alert danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="sell-alert danger">
            <strong>Fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($inventoryItems->count() === 0)
        <section class="sell-empty">
            <h2>No sellable items found.</h2>
            <p>Your inventory may need to be synced again, or your tradable CS2 inventory is empty.</p>

            <form method="POST" action="{{ route('profile.steam.inventory.sync') }}">
                @csrf
                <button type="submit" class="marketplace-button primary">
                    Sync Inventory
                </button>
            </form>
        </section>
    @else
        <form method="POST" action="{{ route('marketplace.listings.store') }}" class="sell-skins-form">
            @csrf

            <section class="sell-settings-card">
                <div>
                    <p class="sell-kicker">Listing Details</p>
                    <h2>Create Listing</h2>
                    <p>Pick an item below, then choose whether this is a trade listing or a priced listing.</p>
                </div>

                <div class="sell-settings-controls">
                    <div class="sell-field">
                        <label for="listing_type">Listing Type</label>
                        <select id="listing_type" name="listing_type">
                            <option value="trade" @selected(old('listing_type', 'trade') === 'trade')>Trade Offer</option>
                            <option value="sale" @selected(old('listing_type') === 'sale')>Sale / Asking Price</option>
                        </select>
                    </div>

                    <div class="sell-field">
                        <label for="asking_price">Asking Price</label>

                        <div class="sell-price-field">
                            <span>$</span>
                            <input
                                id="asking_price"
                                name="asking_price"
                                type="number"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                value="{{ old('asking_price') }}"
                                placeholder="0.00"
                            >
                        </div>

                        <p>Optional for trade listings.</p>
                    </div>
                </div>
            </section>

            <section class="sell-inventory-card">
                <div class="sell-inventory-topbar">
                    <div>
                        <h2>Available Inventory</h2>
                        <p>
                            {{ number_format(method_exists($inventoryItems, 'total') ? $inventoryItems->total() : $inventoryItems->count()) }}
                            item(s) available.
                        </p>
                    </div>

                    <p class="sell-select-hint">Select one item to list.</p>
                </div>

                <div class="sell-inventory-list">
                    @foreach($inventoryItems as $item)
                        @php
                            $rarity = strtolower((string) ($item->rarity ?? 'unknown'));

                            $rarityClass = match (true) {
                                str_contains($rarity, 'consumer') => 'rarity-consumer',
                                str_contains($rarity, 'industrial') => 'rarity-industrial',
                                str_contains($rarity, 'mil-spec'), str_contains($rarity, 'milspec') => 'rarity-milspec',
                                str_contains($rarity, 'restricted') => 'rarity-restricted',
                                str_contains($rarity, 'classified') => 'rarity-classified',
                                str_contains($rarity, 'covert') => 'rarity-covert',
                                str_contains($rarity, 'contraband') => 'rarity-contraband',
                                default => 'rarity-default',
                            };

                            $image = $item->image_url ?? $item->icon_url ?? null;
                        @endphp

                        <label class="sell-inventory-row {{ $rarityClass }}">
                            <input
                                type="radio"
                                name="asset_id"
                                value="{{ $item->asset_id }}"
                                @checked(old('asset_id') == $item->asset_id)
                                required
                            >

                            <div class="sell-radio-dot"></div>

                            <div class="sell-item-image">
                                @if($image)
                                    <img src="{{ $image }}" alt="{{ $item->market_hash_name ?: $item->name }}">
                                @else
                                    <span>CS2</span>
                                @endif
                            </div>

                            <div class="sell-item-info">
                                <div class="sell-item-name-line">
                                    <h3>{{ $item->market_hash_name ?: $item->name }}</h3>
                                    <span>{{ $item->rarity ?: 'Unknown' }}</span>
                                </div>

                                <div class="sell-item-meta">
                                    @if($item->exterior)
                                        <span>{{ $item->exterior }}</span>
                                    @endif

                                    @if($item->type)
                                        <span>{{ $item->type }}</span>
                                    @endif

                                    <span>Asset {{ $item->asset_id }}</span>

                                    @if($item->tradable)
                                        <strong>Tradable</strong>
                                    @else
                                        <strong class="danger">Not Tradable</strong>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if(method_exists($inventoryItems, 'hasPages') && $inventoryItems->hasPages())
                    <div class="sell-pagination">
                        {{ $inventoryItems->links() }}
                    </div>
                @endif
            </section>

            <div class="sell-submit-bar">
                <div>
                    <strong>Publish listing</strong>
                    <p>Select an item above, then publish it to the marketplace.</p>
                </div>

                <button type="submit" class="marketplace-button primary">
                    Publish Listing
                </button>
            </div>
        </form>
    @endif
</section>
@endsection