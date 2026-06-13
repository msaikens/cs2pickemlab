@extends('layouts.app', [
    'title' => 'Terms of Service | CS2 PickLab',
])

@section('content')
<section class="legal-page">
    <article class="legal-card">
        <p class="legal-kicker">Legal</p>

        <h1 class="legal-title">Terms of Service</h1>

        <p class="legal-updated">
            Last updated: {{ now()->format('F j, Y') }}
        </p>

        <div class="legal-content">
            <p>
                These Terms of Service (“Terms”) govern your access to and use of CS2 PickLab, including cs2picklabs.com, dev.cs2picklabs.com, account features, Pick’em content, match pages, shop pages, subscriptions, and related services.
            </p>

            <p>
                By using CS2 PickLab, you agree to these Terms. If you do not agree, do not use the site.
            </p>

            <h2>Eligibility</h2>

            <p>
                You must be able to form a binding agreement to use this site. You may not use CS2 PickLab if prohibited by law or if you are under the age required to use online services in your jurisdiction.
            </p>

            <h2>Accounts</h2>

            <p>
                Some features require an account. You agree to provide accurate information, keep your credentials secure, and notify us of unauthorized account access. You are responsible for activity under your account.
            </p>

            <h2>Google, Apple, ORCID, and Third-Party Login</h2>

            <p>
                We may allow sign-in through third-party identity providers. Your use of those providers is governed by their own terms and privacy policies. We are not responsible for third-party account systems.
            </p>

            <h2>Subscriptions and Paid Features</h2>

            <p>
                Some content or features may require payment, login, or an active subscription. We may change subscription features, pricing, availability, or access rules at any time, subject to applicable law and any terms shown at the time of purchase.
            </p>

            <h2>Shop and Purchases</h2>

            <p>
                Product availability, pricing, taxes, shipping, and fulfillment terms may vary. Some shop links may lead to third-party merchants or affiliate partners. Purchases made through third-party sites are governed by those third parties’ policies.
            </p>

            <h2>No Gambling or Betting Service</h2>

            <p>
                CS2 PickLab provides informational, entertainment, analytical, and community content. We do not operate a sportsbook, gambling platform, wagering service, lottery, or betting exchange. Content on CS2 PickLab should not be treated as betting advice.
            </p>

            <h2>Predictions and Pick’em Content</h2>

            <p>
                Match predictions, Pick’em recommendations, rankings, stats, and commentary are opinions and analysis based on available information. Results are not guaranteed. Esports outcomes are uncertain and may be affected by roster changes, technical issues, format changes, map vetoes, travel, health, tournament rulings, and other factors.
            </p>

            <h2>User Conduct</h2>

            <p>You agree not to:</p>

            <ul>
                <li>Use the site for unlawful, abusive, fraudulent, or harmful purposes;</li>
                <li>Attempt to access accounts, systems, data, or areas you are not authorized to access;</li>
                <li>Scrape, overload, disrupt, or interfere with the site;</li>
                <li>Submit malicious code, spam, or misleading information;</li>
                <li>Impersonate another person or misrepresent your identity;</li>
                <li>Violate intellectual property, privacy, publicity, or other rights.</li>
            </ul>

            <h2>Intellectual Property</h2>

            <p>
                CS2 PickLab content, design, branding, software, analysis, and site materials are owned by us or our licensors unless otherwise stated. You may not copy, redistribute, sell, or exploit site content without permission, except as allowed by law.
            </p>

            <h2>Third-Party Content and Links</h2>

            <p>
                The site may link to third-party websites, merchants, platforms, services, or content. We do not control and are not responsible for third-party sites, products, policies, or actions.
            </p>

            <h2>Service Availability</h2>

            <p>
                We may modify, suspend, discontinue, or restrict any part of the site at any time. We do not guarantee uninterrupted or error-free service.
            </p>

            <h2>Termination</h2>

            <p>
                We may suspend or terminate access if we believe you violated these Terms, created risk, misused the site, or acted unlawfully.
            </p>

            <h2>Disclaimers</h2>

            <p>
                The site is provided “as is” and “as available.” To the fullest extent permitted by law, we disclaim warranties of merchantability, fitness for a particular purpose, non-infringement, accuracy, availability, and reliability.
            </p>

            <h2>Limitation of Liability</h2>

            <p>
                To the fullest extent permitted by law, CS2 PickLab will not be liable for indirect, incidental, consequential, special, exemplary, or punitive damages, or for lost profits, lost data, lost revenue, or business interruption arising from your use of the site.
            </p>

            <h2>Changes to These Terms</h2>

            <p>
                We may update these Terms from time to time. Continued use of the site after changes are posted means you accept the updated Terms.
            </p>

            <h2>Contact</h2>

            <p>
                Questions about these Terms may be sent to:
                <strong>support@cs2picklabs.com</strong>
            </p>
        </div>
    </article>
</section>
@endsection