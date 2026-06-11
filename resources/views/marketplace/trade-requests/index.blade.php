@extends('layouts.app')

@section('title', 'Trade Requests')

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <header class="marketplace-profile-hero">
            <div class="marketplace-profile-kicker">Marketplace</div>
            <h1>Trade Requests</h1>
            <p>Review incoming offers, track sent requests, and mark trades completed.</p>
        </header>

        @if (session('success'))
            <div class="marketplace-alert marketplace-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="marketplace-alert marketplace-alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Incoming</span>
                <h2>Requests From Other Users</h2>
                <p>These are requests on your listed skins.</p>
            </div>

            @if ($incomingRequests->count() === 0)
                <div class="marketplace-empty-state">
                    <strong>No incoming trade requests.</strong>
                    <p>When someone requests one of your listings, it will show here.</p>
                </div>
            @else
                <div class="trade-request-list">
                    @foreach ($incomingRequests as $tradeRequest)
                        @include('marketplace.trade-requests.partials.card', [
                            'tradeRequest' => $tradeRequest,
                            'mode' => 'incoming',
                        ])
                    @endforeach
                </div>

                <div class="marketplace-pagination">
                    {{ $incomingRequests->links() }}
                </div>
            @endif
        </section>

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Sent</span>
                <h2>Your Trade Requests</h2>
                <p>These are requests you sent to other sellers.</p>
            </div>

            @if ($sentRequests->count() === 0)
                <div class="marketplace-empty-state">
                    <strong>No sent trade requests.</strong>
                    <p>Request a trade on an active marketplace listing to start one.</p>
                </div>
            @else
                <div class="trade-request-list">
                    @foreach ($sentRequests as $tradeRequest)
                        @include('marketplace.trade-requests.partials.card', [
                            'tradeRequest' => $tradeRequest,
                            'mode' => 'sent',
                        ])
                    @endforeach
                </div>

                <div class="marketplace-pagination">
                    {{ $sentRequests->links() }}
                </div>
            @endif
        </section>
    </section>
</main>
@endsection