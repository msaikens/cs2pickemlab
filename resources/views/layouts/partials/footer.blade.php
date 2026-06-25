@php
    $legalLinks = config('navigation.footer_legal', []);
    $exploreLinks = config('navigation.footer_explore', []);
@endphp

<footer class="public-footer">
    <div class="public-footer-inner">
        <section class="public-footer-grid">
            <div class="public-footer-column">
                <h2>Legal</h2>

                <nav class="public-footer-nav" aria-label="Legal links">
                    @foreach($legalLinks as $link)
                        <x-navigation-link
                            :route="$link['route']"
                            :label="$link['label']"
                        />
                    @endforeach
                </nav>
            </div>

            <div class="public-footer-column">
                <h2>Explore</h2>

                <nav class="public-footer-nav" aria-label="Explore links">
                    @foreach($exploreLinks as $link)
                        <x-navigation-link
                            :route="$link['route']"
                            :label="$link['label']"
                        />
                    @endforeach
                </nav>
            </div>

            <div class="public-footer-column">
                <h2>CS2 PickLab</h2>

                <div class="public-footer-copy">
                    <p>
                        CS2 PickLab is an independent fan project. It is not affiliated with Valve, Steam, Counter-Strike, tournament organizers, or professional teams.
                    </p>

                    <p>
                        All content, including match data, team information, and player statistics, is sourced from publicly available information and is intended for entertainment purposes only.
                    </p>
                </div>
            </div>
        </section>

        <div class="public-footer-bottom">
            <p>
                &copy; {{ date('Y') }} CS2 PickLab. All rights reserved.
            </p>
        </div>
    </div>
</footer>