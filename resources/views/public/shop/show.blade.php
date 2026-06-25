@extends('layouts.app', ['title' => $product->name . ' | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('content')
<section class="shop-page shop-detail-page">
    <div class="shop-detail-grid">
        <div class="shop-detail-media">
            <div class="shop-detail-image">
                <span>Product Image</span>
            </div>
        </div>

        <div class="shop-detail-main">
            <p class="shop-kicker">{{ ucfirst($product->product_type) }}</p>

            <h1>{{ $product->name }}</h1>

            <p class="shop-detail-description">
                {{ $product->description }}
            </p>

            <p class="shop-detail-price">
                ${{ $product->base_price_dollars }}
            </p>

            <section class="shop-customization-card">
                <div class="shop-card-heading">
                    <p class="shop-kicker">Build Your Award</p>
                    <h2>Customization Options</h2>
                    <p>Choose the options needed to prepare your custom gamer award. Full option pricing will be confirmed during checkout.</p>
                </div>

                <form class="shop-options-form shop-add-cart-form" method="POST" action="{{ route('cart.items.store') }}">
    @csrf

    <input type="hidden" name="product_id" value="{{ $product->id }}">
@if($product->activeVariants->isNotEmpty())
    <div class="shop-field">
        <label for="variant_id">
            Product Option <span>*</span>
        </label>

        <select id="variant_id" name="variant_id" required>
            <option value="">Choose an option</option>

            @foreach($product->activeVariants as $variant)
                <option value="{{ $variant->id }}" @selected(old('variant_id') == $variant->id)>
                    {{ $variant->name }}
                    — ${{ $variant->price_dollars }}

                    @if($variant->inventory_quantity !== null)
                        @if($variant->inventory_quantity <= 0)
                            — Out of stock
                        @elseif($variant->inventory_quantity <= 5)
                            — Only {{ $variant->inventory_quantity }} left
                        @endif
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
    @foreach($product->options as $option)
        <div class="shop-field">
            <label for="option_{{ $option->id }}">
                {{ $option->name }}

                @if($option->is_required)
                    <span>*</span>
                @endif
            </label>

            @if($option->help_text)
                <p class="shop-field-help">{{ $option->help_text }}</p>
            @endif

            @if(in_array($option->type, ['select', 'radio']))
                <select id="option_{{ $option->id }}" name="options[{{ $option->id }}]">
                    <option value="">Choose {{ $option->name }}</option>

                    @foreach($option->values as $value)
                        <option value="{{ $value->id }}">
                            {{ $value->label }}

                            @if($value->price_delta > 0)
                                (+${{ $value->price_delta_dollars }})
                            @endif
                        </option>
                    @endforeach
                </select>
            @elseif($option->type === 'textarea')
                <textarea id="option_{{ $option->id }}" name="options[{{ $option->id }}]" rows="4"></textarea>
            @elseif($option->type === 'file')
                <div class="shop-field-help">
                    File uploads will be collected after checkout for this first cart pass.
                </div>
            @else
                <input
                    id="option_{{ $option->id }}"
                    type="{{ $option->type === 'number' ? 'number' : 'text' }}"
                    name="options[{{ $option->id }}]"
                >
            @endif
        </div>
    @endforeach

    <div class="shop-field">
        <label for="quantity">Quantity</label>

        <input
            id="quantity"
            name="quantity"
            type="number"
            min="1"
            max="99"
            value="1"
            required
        >
    </div>

    @if($product->requires_customization)
        <div class="shop-field">
            <label for="customization_notes">
                Customization notes
            </label>

            <textarea
                id="customization_notes"
                name="customization_notes"
                maxlength="2000"
                rows="4"
                placeholder="Add names, colors, sizing notes, or other details."
            >{{ old('customization_notes') }}</textarea>
        </div>
    @endif

    <button type="submit" class="shop-button primary">
        Add to Cart
    </button>
        </form>
            </section>

            <p class="shop-legal-note">
                Custom products use original designs only. Do not upload official Counter-Strike, Valve, Steam,
                tournament, or pro team artwork unless you own the rights.
            </p>
        </div>
    </div>
</section>
@endsection