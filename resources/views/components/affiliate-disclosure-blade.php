@props([
    'compact' => false,
])

@if(config('monetization.affiliate.enabled'))
    <div {{ $attributes->merge([
        'class' => $compact
            ? 'rounded-lg border border-amber-400/20 bg-amber-400/5 px-4 py-3 text-sm text-amber-100'
            : 'rounded-xl border border-amber-400/20 bg-amber-400/5 p-5 text-sm text-amber-100'
    ]) }}>
        <p class="font-black uppercase tracking-widest text-amber-300">
            Affiliate Disclosure
        </p>

        <p class="mt-2 text-amber-100/90">
            {{ config('monetization.affiliate.disclosure') }}
        </p>
    </div>
@endif