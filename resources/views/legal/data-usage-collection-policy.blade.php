@extends('layouts.app', [
    'title' => 'Data Usage & Collection Policy | CS2 PickLab',
])

@section('content')
<section class="legal-page">
    <div class="legal-card">
        <p class="legal-kicker">
            Legal
        </p>

        <h1 class="legal-title">
            Data Usage & Collection Policy
        </h1>

        <p class="legal-updated">
            Last updated: {{ now()->format('F j, Y') }}
        </p>

        <div class="legal-content">
            <p>
                This Data Usage & Collection Policy explains the categories of data CS2 PickLab may collect, why we collect it, and how it may be used in connection with our website, accounts, Pick’em tools, shop, subscriptions, analytics, advertising, and affiliate features.
            </p>

            <h2>Data You Provide</h2>

            <p>We may collect data you submit directly, including:</p>

            <ul>
                <li>Account name, display name, and email address;</li>
                <li>Password or authentication credentials;</li>
                <li>Profile details such as avatar URL, “About Me” text, and gaming platform names;</li>
                <li>Purchase, order, subscription, or billing-related details;</li>
                <li>Support requests, contact messages, or feedback;</li>
                <li>Pick’em selections, saved preferences, or account activity.</li>
            </ul>

            <h2>Data Collected Automatically</h2>

            <p>We may automatically collect:</p>

            <ul>
                <li>IP address and approximate location derived from IP address;</li>
                <li>Browser, device, operating system, and screen information;</li>
                <li>Pages visited, links clicked, and timestamps;</li>
                <li>Referring URLs and campaign parameters;</li>
                <li>Session identifiers and cookies;</li>
                <li>Error logs, security logs, and performance information.</li>
            </ul>

            <h2>Authentication Provider Data</h2>

            <p>
                If you sign in with Google, Apple, ORCID, or another provider, we may receive information the provider makes available, such as provider account ID, name, email address, and profile image. The exact data depends on the provider, your permissions, and the provider’s policies.
            </p>

            <h2>Analytics Data</h2>

            <p>
                We may use analytics tools to understand site traffic, page performance, user behavior, and feature usage. If Google Analytics is enabled, we will disclose that use and Google’s data processing as required by Google’s policies.
            </p>

            <h2>Advertising Data</h2>

            <p>
                If advertising is enabled, advertising partners may use cookies or similar technologies to provide, measure, and personalize ads. This may include information about your visits to this site and other websites.
            </p>

            <h2>Affiliate Data</h2>

            <p>
                If you click an affiliate link, the affiliate partner or merchant may receive information needed to track referrals, such as a referral ID, link ID, cookie, or purchase attribution data. Purchases through third-party merchants are governed by the merchant’s own policies.
            </p>

            <h2>How Data Is Used</h2>

            <p>We use collected data to:</p>

            <ul>
                <li>Operate and secure the site;</li>
                <li>Authenticate users and maintain sessions;</li>
                <li>Provide user profiles and account features;</li>
                <li>Manage subscriptions, purchases, and restricted content;</li>
                <li>Improve Pick’em, match, team, shop, and recommendation features;</li>
                <li>Send password reset and account-related messages;</li>
                <li>Analyze traffic and performance;</li>
                <li>Serve, measure, or improve advertisements and affiliate placements;</li>
                <li>Detect abuse, fraud, scraping, spam, and unauthorized activity.</li>
            </ul>

            <h2>Legal Bases and Consent</h2>

            <p>
                Depending on your location, we may process data based on your consent, our need to perform a contract with you, compliance with legal obligations, or legitimate interests such as security, analytics, fraud prevention, and site improvement.
            </p>

            <h2>Data Minimization</h2>

            <p>
                We aim to collect only the information reasonably needed to operate CS2 PickLab, provide requested features, secure the platform, and meet legal or business requirements.
            </p>

            <h2>Data Protection</h2>

            <p>
                We use reasonable technical and organizational measures to protect data. No website or online service can guarantee perfect security.
            </p>

            <h2>Contact</h2>

            <p>
                Questions about data collection or usage may be sent to:
                <strong>support@cs2picklabs.com</strong>
            </p>
        </div>
    </div>
</section>
@endsection