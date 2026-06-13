@props([
    'compact' => false,
])

@if(config('monetization.affiliate.enabled'))
    <aside {{ $attributes->merge([
        'class' => $compact
            ? 'affiliate-disclosure compact'
            : 'affiliate-disclosure',
    ]) }}>
        <p>Affiliate Disclosure</p>

        <span>
            {{ config('monetization.affiliate.disclosure') }}
        </span>
    </aside>
@endif