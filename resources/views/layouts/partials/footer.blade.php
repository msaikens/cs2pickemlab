<footer class="public-footer">
    <div class="public-footer-inner">
        <section class="public-footer-grid">
            <div class="public-footer-column">
                <h2>Legal</h2>

                <nav class="public-footer-nav" aria-label="Legal links">
                    <a href="{{ route('legal.privacy') }}">
                        Privacy Policy
                    </a>

                    <a href="{{ route('legal.data') }}">
                        Data Usage &amp; Collection
                    </a>

                    <a href="{{ route('legal.terms') }}">
                        Terms of Service
                    </a>

                    <a href="{{ route('legal.affiliate') }}">
                        Affiliate Disclosures
                    </a>

                    <a href="{{ route('legal.disclaimer') }}">
                        Disclaimer
                    </a>
                </nav>
            </div>

            <div class="public-footer-column">
                <h2>Explore</h2>

                <nav class="public-footer-nav" aria-label="Explore links">
                    <a href="{{ route('home') }}">
                        Home
                    </a>

                    <a href="{{ route('matches.index') }}">
                        Matches
                    </a>

                    <a href="{{ route('pickem.index') }}">
                        Pick&#8217;em
                    </a>

                    <a href="{{ route('teams.index') }}">
                        Teams
                    </a>

                    <a href="{{ route('shop.index') }}">
                        Shop
                    </a>

                    <a href="{{ route('contact.create') }}">
                        Contact Us
                    </a>
                </nav>
            </div>

            <div class="public-footer-column">
                <h2>CS2 PickLab</h2>

                <div class="public-footer-copy">
                    <p>
                        CS2 PickLab is an independent fan project. It is not affiliated with Valve, Steam, Counter Strike, tournament organizers, or professional teams.
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