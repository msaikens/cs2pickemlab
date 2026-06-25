@extends('layouts.app', ['title' => 'Shop | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('content')
<section class="shop-page">
    <header class="shop-hero">
        <p class="shop-kicker">Custom Awards</p>
        <h1>Custom Gamer Awards Shop</h1>
        <p>
            Custom coins, trophies, and award packs for squads, Discord servers, LAN events, and Pick&#8217;em groups.
            Original designs only. No official Counter-Strike, Valve, Steam, tournament, or team marks.
            <b> You will be given a warning for violating copyright or trademark law, and your order will be canceled if you upload
            any copyrighted or trademarked material. Subsequent warning will come with a $15.00 fee and may result in a permanent ban from the shop.</b>
        </p>
    </header>

    @if($products->count() === 0)
        <section class="shop-empty">
            <div class="shop-empty-icon">CS2</div>
            <h2>No products available.</h2>
            <p>Add products from the admin panel to populate the shop.</p>
        </section>
    @else
        <section class="shop-product-grid">
            @foreach($products as $product)
                <a href="{{ route('shop.show', $product) }}" class="shop-product-card">
                    <div class="shop-product-image">
                        <span>Product Image</span>
                    </div>

                    <div class="shop-product-body">
                        <p class="shop-product-type">
                            {{ ucfirst($product->product_type ?? 'Custom Award') }}
                        </p>

                        <h2>{{ $product->name }}</h2>

                        <p class="shop-product-description">
                            {{ $product->short_description }}
                        </p>

                        <div class="shop-product-footer">
                            <strong>${{ $product->base_price_dollars }}</strong>
                            <span>View details →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>

        <div class="shop-pagination">
            {{ $products->links() }}
        </div>
    @endif
</section>
@endsection