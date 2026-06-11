@php
    $listing = $tradeRequest->listing;
    $buyer = $tradeRequest->buyer;
    $seller = $tradeRequest->seller;

    $statusClass = match ($tradeRequest->status) {
        'pending' => 'pending',
        'accepted' => 'accepted',
        'declined' => 'declined',
        'cancelled' => 'cancelled',
        'completed' => 'completed',
        default => 'unknown',
    };

    $viewerIsBuyer = auth()->id() === $tradeRequest->buyer_user_id;
    $viewerIsSeller = auth()->id() === $tradeRequest->seller_user_id;
    $sellerTradeUrl = $seller?->steamTradeProfile?->steam_trade_url;
@endphp

<article class="trade-request-card">
    <div class="trade-request-image">
        @if ($listing?->image_url)
            <img src="{{ $listing->image_url }}" alt="{{ $listing->market_hash_name }}">
        @else
            <div class="marketplace-skin-placeholder">CS2</div>
        @endif
    </div>

    <div class="trade-request-main">
        <div class="trade-request-heading">
            <div>
                <h3>
                    @if ($listing)
                        <a href="{{ route('marketplace.listings.show', $listing) }}">
                            {{ $listing->market_hash_name }}
                        </a>
                    @else
                        Removed Listing
                    @endif
                </h3>

                <p>
                    @if ($mode === 'incoming')
                        From {{ $buyer?->steamAccount?->persona_name ?? $buyer?->displayName() ?? 'Buyer' }}
                    @else
                        To {{ $seller?->steamAccount?->persona_name ?? $seller?->displayName() ?? 'Seller' }}
                    @endif
                </p>
            </div>

            <span class="trade-status {{ $statusClass }}">
                {{ ucfirst($tradeRequest->status) }}
            </span>
        </div>

        @if ($tradeRequest->message)
            <div class="trade-request-message">
                {{ $tradeRequest->message }}
            </div>
        @endif

        @if ($tradeRequest->status === 'accepted')
            <div class="accepted-trade-box">
                @if ($viewerIsBuyer)
                    <strong>Trade accepted.</strong>

                    @if ($sellerTradeUrl)
                        <p>
                            Open the seller’s Steam trade URL and send the offer for this item.
                            Only mark completed after the Steam trade is actually done.
                        </p>

                        <a
                            href="{{ $sellerTradeUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="marketplace-button primary"
                        >
                            Open Seller Trade URL
                        </a>
                    @else
                        <p>
                            The seller does not currently have a trade URL saved. Do not complete this trade yet.
                        </p>
                    @endif
                @elseif ($viewerIsSeller)
                    <strong>You accepted this request.</strong>
                    <p>
                        Wait for the buyer to send the Steam trade offer. Only mark completed after the Steam trade is actually done.
                    </p>
                @endif
            </div>
        @endif

        <div class="trade-request-meta">
            <span>Requested {{ $tradeRequest->created_at?->diffForHumans() }}</span>

            @if ($tradeRequest->accepted_at)
                <span>Accepted {{ $tradeRequest->accepted_at?->diffForHumans() }}</span>
            @endif

            @if ($listing)
                <span>{{ $listing->display_price }}</span>
            @endif
        </div>

        <div class="trade-request-actions">
            @if ($mode === 'incoming')
                @if ($tradeRequest->status === 'pending')
                    <form method="POST" action="{{ route('marketplace.trade-requests.accept', $tradeRequest) }}">
                        @csrf
                        <button type="submit" class="marketplace-button primary">
                            Accept
                        </button>
                    </form>

                    <form method="POST" action="{{ route('marketplace.trade-requests.decline', $tradeRequest) }}">
                        @csrf
                        <button type="submit" class="marketplace-button danger">
                            Decline
                        </button>
                    </form>
                @endif

                @if ($tradeRequest->status === 'accepted')
                    <form
                        method="POST"
                        action="{{ route('marketplace.trade-requests.complete', $tradeRequest) }}"
                        onsubmit="return confirm('Only mark this completed after the Steam trade is actually finished. Continue?');"
                    >
                        @csrf
                        <button type="submit" class="marketplace-button primary">
                            Mark Completed
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('marketplace.trade-requests.decline', $tradeRequest) }}"
                        onsubmit="return confirm('Declining this accepted request will reopen the listing. Continue?');"
                    >
                        @csrf
                        <button type="submit" class="marketplace-button danger">
                            Cancel Accepted Trade
                        </button>
                    </form>
                @endif
            @else
                @if (in_array($tradeRequest->status, ['pending', 'accepted'], true))
                    <form
                        method="POST"
                        action="{{ route('marketplace.trade-requests.cancel', $tradeRequest) }}"
                        onsubmit="return confirm('Cancel this trade request?');"
                    >
                        @csrf
                        <button type="submit" class="marketplace-button danger">
                            Cancel Request
                        </button>
                    </form>
                @endif

                @if ($tradeRequest->status === 'accepted')
                    <form
                        method="POST"
                        action="{{ route('marketplace.trade-requests.complete', $tradeRequest) }}"
                        onsubmit="return confirm('Only mark this completed after the Steam trade is actually finished. Continue?');"
                    >
                        @csrf
                        <button type="submit" class="marketplace-button primary">
                            Mark Completed
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</article>