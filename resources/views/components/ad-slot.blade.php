@props([
    'slot' => null,
    'format' => 'auto',
    'layout' => null,
    'responsive' => true,
    'class' => '',
    'label' => true,
])

@php
    $enabled = (bool) config('monetization.adsense.enabled');
    $client = config('monetization.adsense.client');
    $slotId = $slot ? config("monetization.adsense.slots.$slot") : null;
@endphp

@if($enabled && $client && $slotId)
    <div {{ $attributes->merge(['class' => 'my-6 ' . $class]) }}>
        @if($label)
            <div class="mb-2 text-center text-[10px] font-black uppercase tracking-[0.25em] text-slate-600">
                Advertisement
            </div>
        @endif

        <ins
            class="adsbygoogle block"
            style="display:block"
            data-ad-client="{{ $client }}"
            data-ad-slot="{{ $slotId }}"
            data-ad-format="{{ $format }}"
            data-full-width-responsive="{{ $responsive ? 'true' : 'false' }}"
            @if($layout) data-ad-layout="{{ $layout }}" @endif
        ></ins>

        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
@else
    @if(app()->environment('local'))
        <div {{ $attributes->merge(['class' => 'my-6 rounded-xl border border-dashed border-slate-700 bg-slate-900/40 p-6 text-center ' . $class]) }}>
            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-600">
                Advertisement Placeholder
            </p>
            <p class="mt-2 text-sm text-slate-500">
                Slot: {{ $slot ?? 'unspecified' }}
            </p>
        </div>
    @endif
@endif