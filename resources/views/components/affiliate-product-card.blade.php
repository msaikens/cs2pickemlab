@props([
    'title',
    'description' => null,
    'url',
    'image' => null,
    'price' => null,
    'merchant' => null,
    'badge' => 'Affiliate',
])

<article {{ $attributes->merge(['class' => 'affiliate-product-card']) }}>
    @if($image)
        <a href="{{ $url }}" target="_blank" rel="nofollow sponsored noopener" class="affiliate-product-image">
            <img src="{{ $image }}" alt="{{ $title }}">
        </a>
    @endif

    <div class="affiliate-product-header">
        <div>
            <p>{{ $merchant ?? 'Recommended Product' }}</p>
            <h3>{{ $title }}</h3>
        </div>

        <span>{{ $badge }}</span>
    </div>

    @if($description)
        <p class="affiliate-product-description">
            {{ $description }}
        </p>
    @endif

    @if($price)
        <p class="affiliate-product-price">
            {{ $price }}
        </p>
    @endif

    <a
        href="{{ $url }}"
        target="_blank"
        rel="nofollow sponsored noopener"
        class="affiliate-product-button"
    >
        View Product
    </a>
</article>