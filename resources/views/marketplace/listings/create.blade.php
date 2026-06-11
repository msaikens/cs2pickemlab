@extends('layouts.app')

@section('title', 'Create Skin Listing')

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <header class="marketplace-profile-hero">
            <div class="marketplace-profile-kicker">Marketplace</div>
            <h1>Create Listing</h1>
            <p>Select a tradable item from your synced Steam inventory.</p>
        </header>

        @if (session('error'))
            <div class="marketplace-alert marketplace-alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="marketplace-alert marketplace-alert-danger">
                <strong>Fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Synced Inventory</span>
                <h2>Choose an Item to List</h2>
                <p>Only tradable items currently visible in your Steam inventory appear here.</p>
            </div>

            @if ($items->count() === 0)
                <div class="marketplace-empty-state">
                    <strong>No tradable synced items found.</strong>
                    <p>Go back to your Steam profile and run inventory sync again.</p>
                </div>
            @else
                <div class="inventory-listing-grid">
                    @foreach ($items as $item)
                        <form method="POST" action="{{ route('marketplace.listings.store') }}" class="inventory-listing-card">
                            @csrf

                            <input type="hidden" name="asset_id" value="{{ $item->asset_id }}">

                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->market_hash_name }}">
                            @endif

                            <h3>{{ $item->market_hash_name }}</h3>

                            <div class="inventory-meta">
                                <span>{{ $item->rarity ?? 'Unknown rarity' }}</span>
                                <span>{{ $item->exterior ?? 'No exterior' }}</span>
                            </div>

                            <label>
                                Listing Type
                                <select name="listing_type" required>
                                    <option value="trade">Trade Only</option>
                                    <option value="sale">Sale</option>
                                </select>
                            </label>

                            <label>
                                Asking Price
                                <input
                                    type="number"
                                    name="asking_price"
                                    min="0"
                                    step="0.01"
                                    placeholder="Optional for trade"
                                >
                            </label>

                            <button type="submit" class="marketplace-button primary">
                                List Item
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="marketplace-pagination">
                    {{ $items->links() }}
                </div>
            @endif
        </section>
    </section>
</main>
@endsection