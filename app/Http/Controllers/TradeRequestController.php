<?php

namespace App\Http\Controllers;

use App\Models\SkinListing;
use App\Models\TradeRequest;
use App\Models\TradeRequestEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TradeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $incomingRequests = TradeRequest::query()
            ->with([
                'listing',
                'buyer.steamAccount',
                'buyer.steamTradeProfile',
                'seller.steamAccount',
                'seller.steamTradeProfile',
                'events.actor',
            ])
            ->where('seller_user_id', $request->user()->id)
            ->latest()
            ->paginate(10, ['*'], 'incoming_page');

        $sentRequests = TradeRequest::query()
            ->with([
                'listing',
                'buyer.steamAccount',
                'buyer.steamTradeProfile',
                'seller.steamAccount',
                'seller.steamTradeProfile',
                'events.actor',
            ])
            ->where('buyer_user_id', $request->user()->id)
            ->latest()
            ->paginate(10, ['*'], 'sent_page');

        return view('marketplace.trade-requests.index', [
            'incomingRequests' => $incomingRequests,
            'sentRequests' => $sentRequests,
        ]);
    }

    public function store(Request $request, SkinListing $listing): RedirectResponse
    {
        abort_unless($listing->status === 'active', 404);

        if ($listing->user_id === $request->user()->id) {
            return back()->with('error', 'You cannot request a trade on your own listing.');
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = TradeRequest::where('skin_listing_id', $listing->id)
            ->where('buyer_user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'You already have an open request for this listing.');
        }

        $tradeRequest = DB::transaction(function () use ($request, $listing, $validated): TradeRequest {
            $tradeRequest = TradeRequest::create([
                'skin_listing_id' => $listing->id,
                'buyer_user_id' => $request->user()->id,
                'seller_user_id' => $listing->user_id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'requested',
                null,
                'pending',
                [
                    'listing_id' => $listing->id,
                    'asset_id' => $listing->steam_asset_id,
                    'listing_status' => $listing->status,
                ]
            );

            return $tradeRequest;
        });

        return redirect()
            ->route('marketplace.listings.show', $listing)
            ->with('success', 'Trade request sent.');
    }

    public function accept(Request $request, TradeRequest $tradeRequest): RedirectResponse
    {
        $this->authorizeSeller($request, $tradeRequest);

        if ($tradeRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be accepted.');
        }

        $accepted = DB::transaction(function () use ($request, $tradeRequest): bool {
            $tradeRequest->load('listing');

            if (! $tradeRequest->listing || $tradeRequest->listing->status !== 'active') {
                return false;
            }

            $oldTradeStatus = $tradeRequest->status;
            $oldListingStatus = $tradeRequest->listing->status;

            $tradeRequest->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'accepted',
                $oldTradeStatus,
                'accepted',
                [
                    'listing_id' => $tradeRequest->skin_listing_id,
                    'asset_id' => $tradeRequest->listing->steam_asset_id,
                ]
            );

            $tradeRequest->listing->update([
                'status' => 'pending',
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'listing_pending',
                $oldListingStatus,
                'pending',
                [
                    'listing_id' => $tradeRequest->skin_listing_id,
                    'asset_id' => $tradeRequest->listing->steam_asset_id,
                    'reason' => 'trade_request_accepted',
                ]
            );

            $otherPendingRequests = TradeRequest::where('skin_listing_id', $tradeRequest->skin_listing_id)
                ->where('id', '!=', $tradeRequest->id)
                ->where('status', 'pending')
                ->get();

            foreach ($otherPendingRequests as $otherRequest) {
                $oldOtherStatus = $otherRequest->status;

                $otherRequest->update([
                    'status' => 'declined',
                    'declined_at' => now(),
                ]);

                $this->logTradeEvent(
                    $otherRequest,
                    $request->user()->id,
                    'auto_declined_due_to_other_acceptance',
                    $oldOtherStatus,
                    'declined',
                    [
                        'listing_id' => $tradeRequest->skin_listing_id,
                        'accepted_trade_request_id' => $tradeRequest->id,
                    ]
                );
            }

            return true;
        });

        if (! $accepted) {
            return back()->with('error', 'That listing is no longer active.');
        }

        return back()->with('success', 'Trade request accepted. The buyer can now open your Steam trade URL.');
    }

    public function decline(Request $request, TradeRequest $tradeRequest): RedirectResponse
    {
        $this->authorizeSeller($request, $tradeRequest);

        if (! in_array($tradeRequest->status, ['pending', 'accepted'], true)) {
            return back()->with('error', 'That request cannot be declined.');
        }

        DB::transaction(function () use ($request, $tradeRequest): void {
            $tradeRequest->load('listing');

            $oldTradeStatus = $tradeRequest->status;
            $wasAccepted = $tradeRequest->status === 'accepted';

            $tradeRequest->update([
                'status' => 'declined',
                'declined_at' => now(),
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'declined',
                $oldTradeStatus,
                'declined',
                [
                    'listing_id' => $tradeRequest->skin_listing_id,
                    'was_accepted' => $wasAccepted,
                ]
            );

            if ($wasAccepted && $tradeRequest->listing?->status === 'pending') {
                $oldListingStatus = $tradeRequest->listing->status;

                $tradeRequest->listing->update([
                    'status' => 'active',
                ]);

                $this->logTradeEvent(
                    $tradeRequest,
                    $request->user()->id,
                    'listing_reopened',
                    $oldListingStatus,
                    'active',
                    [
                        'listing_id' => $tradeRequest->skin_listing_id,
                        'reason' => 'accepted_trade_declined_by_seller',
                    ]
                );
            }
        });

        return back()->with('success', 'Trade request declined.');
    }

    public function cancel(Request $request, TradeRequest $tradeRequest): RedirectResponse
    {
        $this->authorizeBuyer($request, $tradeRequest);

        if (! in_array($tradeRequest->status, ['pending', 'accepted'], true)) {
            return back()->with('error', 'That request cannot be cancelled.');
        }

        DB::transaction(function () use ($request, $tradeRequest): void {
            $tradeRequest->load('listing');

            $oldTradeStatus = $tradeRequest->status;
            $wasAccepted = $tradeRequest->status === 'accepted';

            $tradeRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'cancelled',
                $oldTradeStatus,
                'cancelled',
                [
                    'listing_id' => $tradeRequest->skin_listing_id,
                    'was_accepted' => $wasAccepted,
                ]
            );

            if ($wasAccepted && $tradeRequest->listing?->status === 'pending') {
                $oldListingStatus = $tradeRequest->listing->status;

                $tradeRequest->listing->update([
                    'status' => 'active',
                ]);

                $this->logTradeEvent(
                    $tradeRequest,
                    $request->user()->id,
                    'listing_reopened',
                    $oldListingStatus,
                    'active',
                    [
                        'listing_id' => $tradeRequest->skin_listing_id,
                        'reason' => 'accepted_trade_cancelled_by_buyer',
                    ]
                );
            }
        });

        return back()->with('success', 'Trade request cancelled.');
    }

    public function complete(Request $request, TradeRequest $tradeRequest): RedirectResponse
    {
        $isBuyer = $tradeRequest->buyer_user_id === $request->user()->id;
        $isSeller = $tradeRequest->seller_user_id === $request->user()->id;

        abort_unless($isBuyer || $isSeller, 403);

        if ($tradeRequest->status !== 'accepted') {
            return back()->with('error', 'Only accepted requests can be marked completed.');
        }

        DB::transaction(function () use ($request, $tradeRequest): void {
            $tradeRequest->load('listing');

            $oldTradeStatus = $tradeRequest->status;

            $tradeRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->logTradeEvent(
                $tradeRequest,
                $request->user()->id,
                'completed',
                $oldTradeStatus,
                'completed',
                [
                    'listing_id' => $tradeRequest->skin_listing_id,
                    'completed_by_role' => $request->user()->id === $tradeRequest->buyer_user_id ? 'buyer' : 'seller',
                ]
            );

            if ($tradeRequest->listing) {
                $oldListingStatus = $tradeRequest->listing->status;

                $tradeRequest->listing->update([
                    'status' => 'completed',
                ]);

                $this->logTradeEvent(
                    $tradeRequest,
                    $request->user()->id,
                    'listing_completed',
                    $oldListingStatus,
                    'completed',
                    [
                        'listing_id' => $tradeRequest->skin_listing_id,
                        'asset_id' => $tradeRequest->listing->steam_asset_id,
                    ]
                );
            }
        });

        return back()->with('success', 'Trade marked completed.');
    }

    private function authorizeSeller(Request $request, TradeRequest $tradeRequest): void
    {
        abort_unless($tradeRequest->seller_user_id === $request->user()->id, 403);
    }

    private function authorizeBuyer(Request $request, TradeRequest $tradeRequest): void
    {
        abort_unless($tradeRequest->buyer_user_id === $request->user()->id, 403);
    }

    private function logTradeEvent(
        TradeRequest $tradeRequest,
        ?int $actorUserId,
        string $eventType,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        array $metadata = []
    ): void {
        TradeRequestEvent::create([
            'trade_request_id' => $tradeRequest->id,
            'actor_user_id' => $actorUserId,
            'event_type' => $eventType,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'metadata' => $metadata ?: null,
        ]);
    }
}