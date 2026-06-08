@props([
    'title',
    'description' => null,
    'url',
    'image' => null,
    'price' => null,
    'merchant' => null,
    'badge' => 'Affiliate',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-800 bg-slate-900/70 p-4']) }}>
    @if($image)
        <a href="{{ $url }}" target="_blank" rel="nofollow sponsored noopener">
            <img src="{{ $image }}" alt="{{ $title }}" class="h-40 w-full rounded-xl object-cover">
        </a>
    @endif

    <div class="mt-4 flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-cyan-400">
                {{ $merchant ?? 'Recommended Product' }}
            </p>

            <h3 class="mt-1 text-lg font-black text-white">
                {{ $title }}
            </h3>
        </div>

        <span class="shrink-0 rounded-full border border-amber-400/30 bg-amber-400/10 px-2 py-1 text-[10px] font-black uppercase text-amber-200">
            {{ $badge }}
        </span>
    </div>

    @if($description)
        <p class="mt-3 text-sm text-slate-400">
            {{ $description }}
        </p>
    @endif

    @if($price)
        <p class="mt-3 text-xl font-black text-white">
            {{ $price }}
        </p>
    @endif

    <a
        href="{{ $url }}"
        target="_blank"
        rel="nofollow sponsored noopener"
        class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-cyan-400 px-4 py-3 font-black text-slate-950 hover:bg-cyan-300"
    >
        View Product
    </a>
</div>