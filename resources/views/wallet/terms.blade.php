@extends('layouts.public', [
    'title' => 'Wallet Terms | CS2 PickLab',
    'pageTitle' => 'Wallet Terms',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wallet-terms.css') }}">
@endpush

@section('content')
<section class="wallet-terms-page">
    <header class="wallet-terms-hero">
        <div>
            <p class="wallet-terms-kicker">Wallet Policy</p>
            <h1>Wallet Terms</h1>
            <p>
                Review how CS2 PickLab wallet funds, seller proceeds, refunds, and withdrawals work before using wallet features.
            </p>
        </div>

        <div class="wallet-terms-version">
            Version: <strong>{{ $termsVersion }}</strong>
        </div>
    </header>

    @if(session('warning'))
        <div class="wallet-terms-alert warning">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('status'))
        <div class="wallet-terms-alert">
            {{ session('status') }}
        </div>
    @endif

    @if($hasAccepted)
        <div class="wallet-terms-alert success">
            You have already accepted the current Wallet Terms version.
        </div>
    @endif

    <section class="wallet-terms-card">
        <h2>1. Wallet top-ups are site credit</h2>
        <p>
            Wallet top-ups add spendable CS2 PickLab site credit to your account. Top-up balance is intended for eligible CS2 PickLab purchases and marketplace activity.
        </p>
        <p>
            Top-up balance is not cash, is not a bank account, does not earn interest, and is not directly withdrawable.
        </p>
    </section>

    <section class="wallet-terms-card">
        <h2>2. Seller proceeds are separate</h2>
        <p>
            Seller proceeds are tracked separately from top-up funds. Seller proceeds may become withdrawable only after applicable review, settlement, fraud checks, trade completion checks, dispute windows, platform holds, and any required compliance review.
        </p>
        <p>
            CS2 PickLab may delay, deny, reverse, or hold seller proceeds when activity appears fraudulent, abusive, mistaken, disputed, restricted, or otherwise unsafe for the marketplace.
        </p>
    </section>

    <section class="wallet-terms-card">
        <h2>3. Refunds for unused top-up funds</h2>
        <p>
            Unused top-up funds may be refunded to the original payment method when eligible. Refund eligibility may depend on payment processor rules, fraud review, chargeback status, account standing, bonus credits, promotional credits, prior wallet usage, and whether the funds have already been spent.
        </p>
        <p>
            Refunds are not guaranteed in every case. CS2 PickLab may require review before issuing a refund.
        </p>
    </section>

    <section class="wallet-terms-card">
        <h2>4. Holds, reversals, and account review</h2>
        <p>
            CS2 PickLab may place wallet funds, seller proceeds, listings, purchases, withdrawals, or account access under review to protect users and the marketplace.
        </p>
        <p>
            Wallet actions may be blocked or delayed if there are suspected payment issues, trade disputes, marketplace abuse, policy violations, chargebacks, unauthorized activity, or technical errors.
        </p>
    </section>

    <section class="wallet-terms-card">
        <h2>5. Acceptance record</h2>
        <p>
            When you accept these Wallet Terms, CS2 PickLab stores an audit record that includes your user ID, the accepted terms version, acceptance time, IP address, user agent, and acceptance source.
        </p>
    </section>

    <section class="wallet-terms-accept-card">
        @auth
            <form method="POST" action="{{ route('wallet.terms.accept') }}" class="wallet-terms-form">
                @csrf

                <input type="hidden" name="source" value="{{ $source }}">

                <label class="wallet-terms-checkbox">
                    <input
                        type="checkbox"
                        name="accepted"
                        value="1"
                        required
                    >
                    <span>
                        I have read and agree to the current CS2 PickLab Wallet Terms.
                    </span>
                </label>

                @error('accepted')
                    <p class="wallet-terms-error">{{ $message }}</p>
                @enderror

                <div class="wallet-terms-actions">
                    <button type="submit" class="wallet-terms-button primary">
                        Accept Wallet Terms
                    </button>

                    <a href="{{ route('account.wallet') }}" class="wallet-terms-button secondary">
                        Back to Wallet
                    </a>
                </div>
            </form>
        @else
            <p>
                You can read these terms publicly. Sign in to accept them for your account.
            </p>

            <div class="wallet-terms-actions">
                <a href="{{ route('login') }}" class="wallet-terms-button primary">
                    Sign In to Accept
                </a>
            </div>
        @endauth
    </section>
</section>
@endsection