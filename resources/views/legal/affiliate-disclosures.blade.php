@extends('layouts.app', [
    'title' => 'Affiliate Disclosures | CS2 PickLab',
])

@section('content')
<section class="legal-page">
    <div class="legal-card">
        <p class="legal-kicker">
            Legal
        </p>

        <h1 class="legal-title">
            Affiliate Disclosures
        </h1>

        <p class="legal-updated">
            Last updated: {{ now()->format('F j, Y') }}
        </p>

        <div class="legal-content">            
            <p>
                CS2 PickLab may participate in affiliate marketing programs. This means we may earn a commission when you click certain links and purchase products or services from third-party merchants. This does not increase the price you pay.
            </p>

            <h2>Clear Disclosure</h2>

            <p>
                When a page contains affiliate links, we aim to disclose that relationship clearly near the relevant content, product cards, recommendations, or links. Affiliate disclosures are intended to help users understand when CS2 PickLab may have a financial relationship with a merchant or product provider.
            </p>

            <h2>Affiliate Compensation</h2>

            <p>
                We may receive compensation through affiliate links, referral links, sponsored links, tracked URLs, discount codes, or partner programs.
            </p>

            <h2>Editorial Independence</h2>

            <p>
                Affiliate compensation may influence which products or merchants appear on the site, but we aim to avoid intentionally misleading users. Product mentions, recommendations, or comparisons should not be interpreted as guarantees of quality, suitability, availability, pricing, or performance.
            </p>

            <h2>Third-Party Merchants</h2>

            <p>
                Purchases made through affiliate links are completed with third-party merchants. We are not responsible for third-party pricing, shipping, returns, warranties, product quality, customer service, privacy practices, or terms of sale.
            </p>

            <h2>No Professional Purchasing Advice</h2>

            <p>
                Product recommendations are informational and based on available information, personal judgment, user needs, popularity, or relevance to CS2/esports audiences. You should review product details, merchant policies, and independent reviews before purchasing.
            </p>

            <h2>Examples of Affiliate Content</h2>

            <p>Affiliate content may include:</p>

            <ul>
                <li>Gaming peripherals and hardware;</li>
                <li>Computer parts or accessories;</li>
                <li>Software tools;</li>
                <li>Merchandise or apparel;</li>
                <li>Books, guides, or training products;</li>
                <li>Esports-related services or platforms.</li>
            </ul>

            <h2>Contact</h2>

            <p>
                Questions about affiliate relationships may be sent to:
                <strong>support@cs2picklabs.com</strong>
            </p>
        </div>
    </div>
</section>
@endsection