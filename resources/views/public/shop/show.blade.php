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
                    <p>Choose the options needed to prepare your custom gamer award.</p>
                </div>

                <form class="shop-options-form" method="POST" action="#" enctype="multipart/form-data">
                    @csrf

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
                                <input id="option_{{ $option->id }}" type="file" name="uploads[{{ $option->id }}]">
                            @else
                                <input
                                    id="option_{{ $option->id }}"
                                    type="{{ $option->type === 'number' ? 'number' : 'text' }}"
                                    name="options[{{ $option->id }}]"
                                >
                            @endif
                        </div>
                    @endforeach

                    <button type="button" class="shop-button primary">
                        Checkout coming next
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