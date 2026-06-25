@extends('layouts.public', [
    'title' => 'Order Confirmation | CS2 PickLab',
    'pageTitle' => 'Order Confirmation',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-cart.css') }}">
@endpush

@section('content')
<section class="shop-cart-page">
    <section class="shop-cart-empty">
        <p class="shop-cart-kicker">Order Confirmation</p>

        <h1>Thanks for your order.</h1>

        @if($order)
            <p>
                Your order number is <strong>{{ $order->order_number }}</strong>.
            </p>

            <p>
                Total: <strong>${{ $order->total_dollars }}</strong>
            </p>
        @else
            <p>
                Order confirmation is ready, but no order record was provided yet.
            </p>
        @endif

        <a href="{{ route('shop.index') }}" class="shop-cart-button primary">
            Back to Shop
        </a>
    </section>
</section>
@endsection