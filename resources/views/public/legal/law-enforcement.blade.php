@extends('layouts.public', [
    'title' => 'Law Enforcement & Fraud Requests | CS2 PickLab',
    'pageTitle' => 'Law Enforcement & Fraud Requests',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
@endpush

@section('content')
<section class="legal-page">
    <header class="legal-hero">
        <p class="legal-kicker">Trust & Safety</p>

        <h1>Law Enforcement & Fraud Requests</h1>

        <p>
            CS2 PickLab takes marketplace abuse, payment fraud, stolen payment methods,
            account compromise, and unlawful activity seriously. This page explains how
            law enforcement, financial institutions, and affected users can contact us.
        </p>
    </header>

    <article class="legal-card" id="law-enforcement-requests">
        <h2>Law Enforcement Requests</h2>

        <p>
            Authorized law enforcement officials may contact CS2 PickLab regarding valid
            legal process, preservation requests, fraud investigations, account compromise,
            marketplace abuse, or emergency safety concerns.
        </p>

        <p>
            To help us review a request efficiently, include the agency name, officer or
            investigator name, badge or employee identification if applicable, official
            agency email address, case number, requested records, relevant usernames,
            order numbers, transaction IDs, listing IDs, dates, and the legal authority
            supporting the request.
        </p>

        <div class="legal-callout">
            <strong>Law enforcement contact:</strong>
            <p>
                Email: <a href="mailto:lawenforcement@cs2picklabs.com">lawenforcement@cs2picklabs.com</a>
            </p>
            <p>
                Please send requests from an official government or agency email address when possible.
            </p>
        </div>

        <p>
            CS2 PickLab may require valid legal process before disclosing non-public user
            information. We may preserve relevant account, transaction, marketplace, and
            access records when legally appropriate.
        </p>
    </article>

    <article class="legal-card" id="payment-fraud">
        <h2>Payment Fraud and Stolen Card Reports</h2>

        <p>
            If you believe a stolen credit card, unauthorized payment method, fraudulent
            purchase, chargeback abuse, or payment-related scam was attempted or completed
            through CS2 PickLab, report it promptly.
        </p>

        <p>
            Include any order number, Stripe payment reference if available, account email,
            username, approximate transaction time, item or listing involved, and a clear
            explanation of what happened.
        </p>

        <div class="legal-callout warning">
            <strong>Fraud contact:</strong>
            <p>
                Email: <a href="mailto:fraud@cs2picklabs.com">fraud@cs2picklabs.com</a>
            </p>
        </div>

        <p>
            We may suspend marketplace access, pause fulfillment, preserve records, cancel
            suspicious transactions, cooperate with payment processors, and provide records
            in response to valid legal process.
        </p>
    </article>

    <article class="legal-card" id="marketplace-fraud">
        <h2>Marketplace Fraud and Account Abuse</h2>

        <p>
            Report suspected scams, stolen Steam accounts, fake trade offers, impersonation,
            off-platform payment pressure, coercion, harassment, or attempts to manipulate
            marketplace ratings or dispute systems.
        </p>

        <p>
            Provide the listing URL, trade request details, usernames, screenshots,
            timestamps, Steam profile links if relevant, and a short description of the issue.
        </p>

        <div class="legal-callout">
            <strong>Marketplace abuse contact:</strong>
            <p>
                Email: <a href="mailto:fraud@cs2picklabs.com">fraud@cs2picklabs.com</a>
            </p>
        </div>
    </article>

    <article class="legal-card" id="emergency-requests">
        <h2>Emergency Requests</h2>

        <p>
            If there is an imminent risk of death, serious physical injury, or active threat,
            contact local emergency services first. Law enforcement may contact us with
            emergency requests involving CS2 PickLab accounts or marketplace activity.
        </p>

        <p>
            Emergency requests should clearly identify the nature of the emergency, the
            specific user or transaction involved, the information requested, and why the
            request is urgent.
        </p>
    </article>

    <article class="legal-card" id="user-notice">
        <h2>User Notice</h2>

        <p>
            CS2 PickLab may notify users about legal requests for their information unless
            prohibited by law, court order, emergency circumstances, or risk of harm,
            fraud, or abuse.
        </p>

        <p>
            This page is informational and does not create a contractual right, guarantee
            disclosure, or waive any legal objection CS2 PickLab may have.
        </p>
    </article>
</section>
@endsection