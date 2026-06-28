@php
    $makeFooterLinks = function (string $key) {
        return collect(config("navigation.{$key}", []))
            ->filter(fn ($item) => isset($item['route']) && Route::has($item['route']));
    };

    $hrefFor = function (array $link) {
        $href = route($link['route']);

        if (! empty($link['fragment'])) {
            $href .= '#' . ltrim($link['fragment'], '#');
        }

        return $href;
    };

    $exploreLinks = $makeFooterLinks('footer_explore');
    $legalLinks = $makeFooterLinks('footer_legal');
    $lawLinks = $makeFooterLinks('footer_law_enforcement');
@endphp

<footer class="site-footer">
    <div class="site-footer-grid">
        <section class="site-footer-brand">
            <a href="{{ route('home') }}" class="site-footer-logo">
                CS2 PickLab
            </a>

            <p>
                CS2 picks, marketplace tools, shop items, and community features built for smarter Counter-Strike fans.
            </p>
        </section>

        <section class="site-footer-column">
            <h2>Explore</h2>

            <nav class="site-footer-links" aria-label="Explore">
                @foreach($exploreLinks as $link)
                    <a href="{{ $hrefFor($link) }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </section>

        <section class="site-footer-column">
            <h2>Legal</h2>

            <nav class="site-footer-links" aria-label="Legal">
                @foreach($legalLinks as $link)
                    <a href="{{ $hrefFor($link) }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </section>

        <section class="site-footer-column site-footer-law">
            <h2>Law Enforcement</h2>

            <nav class="site-footer-links" aria-label="Law Enforcement">
                @foreach($lawLinks as $link)
                    <a href="{{ $hrefFor($link) }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </section>
    </div>

    <div class="site-footer-bottom">
        <span>&copy; {{ now()->year }} CS2 PickLab. All rights reserved.</span>
        <span>Not affiliated with Valve Corporation.</span>
    </div>
</footer>