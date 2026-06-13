@extends('layouts.app', [
    'title' => 'Disclaimer | CS2 PickLab',
])

@section('content')
<section class="legal-page">
    <article class="legal-card">
        <p class="legal-kicker">Legal</p>

        <h1 class="legal-title">Disclaimer</h1>

        <p class="legal-updated">
            Last updated: {{ now()->format('F j, Y') }}
        </p>

        <div class="legal-content">
            <h2>General Information Only</h2>

            <p>
                CS2 PickLab provides esports information, match content, Pick’em recommendations, analysis, commentary, product links, and related content for informational and entertainment purposes only.
            </p>

            <h2>No Guaranteed Results</h2>

            <p>
                Match predictions, Pick’em picks, advancement projections, brackets, rankings, and recommendations are not guarantees. Esports results can change quickly and may be affected by team performance, roster changes, tournament rules, map vetoes, technical issues, travel, illness, scheduling, format changes, or other factors.
            </p>

            <h2>No Gambling or Betting Advice</h2>

            <p>
                CS2 PickLab is not a gambling service, betting platform, sportsbook, casino, lottery, or wagering service. Nothing on this site should be interpreted as gambling advice, betting advice, financial advice, or encouragement to place a wager.
            </p>

            <h2>No Financial, Legal, or Professional Advice</h2>

            <p>
                Content on this site is not financial, legal, tax, investment, professional, or technical consulting advice. You should consult qualified professionals before making decisions that require professional judgment.
            </p>

            <h2>Product and Affiliate Disclaimer</h2>

            <p>
                Product references, affiliate links, and recommendations are provided for convenience and informational purposes. We do not guarantee product quality, availability, pricing, suitability, shipping, returns, warranties, or merchant performance.
            </p>

            <h2>Third-Party Links</h2>

            <p>
                CS2 PickLab may link to third-party websites, services, merchants, social platforms, statistics providers, payment processors, or advertising partners. We are not responsible for third-party content, policies, actions, or availability.
            </p>

            <h2>Accuracy and Availability</h2>

            <p>
                We aim to provide useful and accurate information, but errors, delays, omissions, or outdated information may occur. We do not guarantee that the site or its content will always be accurate, complete, secure, available, or uninterrupted.
            </p>

            <h2>Use at Your Own Risk</h2>

            <p>
                Your use of CS2 PickLab is at your own risk. You are responsible for evaluating content, products, links, recommendations, and any decisions you make based on information from the site.
            </p>

            <h2>Contact</h2>

            <p>
                Questions about this Disclaimer may be sent to:
                <strong>support@cs2picklabs.com</strong>
            </p>
        </div>
    </article>
</section>
@endsection