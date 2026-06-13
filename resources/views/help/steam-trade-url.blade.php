@extends('layouts.public', [
    'title' => 'How to Find Your Steam Trade URL | CS2 PickLab',
    'pageTitle' => 'How to Find Your Steam Trade URL',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/help.css') }}">
@endpush

@section('content')
<section class="help-page">
    <header class="help-hero">
        <p class="help-kicker">Steam Marketplace Setup</p>

        <h1>How to Find Your Steam Trade URL</h1>

        <p>
            Your Steam Trade URL lets another Steam user send you a trade offer directly.
            CS2 PickLab uses it so accepted marketplace trades can be completed on Steam.
        </p>
    </header>

    <section class="help-card">
        <div class="help-section-heading">
            <p class="help-kicker">Walkthrough</p>
            <h2>Step-by-step</h2>
        </div>

        <ol class="help-steps">
            <li>
                <strong>Open Steam in your browser.</strong>
                <p>Go to Steam and make sure you are signed into the correct account.</p>
            </li>

            <li>
                <strong>Open your Inventory.</strong>
                <p>
                    Hover over your Steam username, then choose <strong>Inventory</strong>.
                </p>
            </li>

            <li>
                <strong>Click Trade Offers.</strong>
                <p>
                    On the Inventory page, click <strong>Trade Offers</strong>.
                </p>
            </li>

            <li>
                <strong>Click &#8220;Who can send me Trade Offers?&#8221;</strong>
                <p>
                    This page contains your personal Steam Trade URL.
                </p>
            </li>

            <li>
                <strong>Copy your Trade URL.</strong>
                <p>
                    It should look similar to this:
                </p>

                <code class="help-code">
                    https://steamcommunity.com/tradeoffer/new/?partner=123456789&amp;token=AbCdEfGh
                </code>
            </li>

            <li>
                <strong>Paste it into CS2 PickLab.</strong>
                <p>
                    Return to your Steam marketplace setup page and paste the full URL into the Trade URL field.
                </p>
            </li>
        </ol>
    </section>

    <section class="help-callout warning">
        <h2>Important privacy note</h2>

        <p>
            Your Steam profile and CS2 inventory must both be public for marketplace selling to work.
            A public profile alone is not enough. Steam inventory privacy is controlled separately.
        </p>
    </section>

    <section class="help-card">
        <div class="help-section-heading">
            <p class="help-kicker">Before You Sell</p>
            <h2>Quick checklist</h2>
        </div>

        <div class="help-checklist">
            <div>Steam account linked</div>
            <div>Steam profile set to public</div>
            <div>CS2 inventory set to public</div>
            <div>Trade URL saved</div>
        </div>
    </section>

    <div class="help-actions">
        @auth
            @if(Route::has('profile.steam'))
                <a href="{{ route('profile.steam') }}" class="help-button primary">
                    Back to Steam Setup
                </a>
            @endif
        @endauth

        <a
            href="https://steamcommunity.com/my/tradeoffers/privacy"
            target="_blank"
            rel="noopener noreferrer"
            class="help-button secondary"
        >
            Open Steam Trade URL Page
        </a>
    </div>
</section>
@endsection