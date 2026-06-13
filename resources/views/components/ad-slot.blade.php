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
    <div {{ $attributes->merge(['class' => trim('ad-slot ' . $class)]) }}>
        @if($label)
            <div class="ad-slot-label">
                Advertisement
            </div>
        @endif

        <ins
            class="adsbygoogle ad-slot-unit"
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
@elseif(app()->environment('local'))
    <div {{ $attributes->merge(['class' => trim('ad-slot ad-slot-placeholder ' . $class)]) }}>
        <p>Advertisement Placeholder</p>
        <span>Slot: {{ $slot ?? 'unspecified' }}</span>
    </div>
@endif