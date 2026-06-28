<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkinListing;
use App\Models\TradeRequest;
use App\Models\TradeRequestEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarketplaceModerationController extends Controller
{
    public function listings(Request $request): View
    {
        $listings = SkinListing::query()
            ->with([
                'user.profile',
                'user.steamAccount',
                'tradeRequests.buyer.steamAccount',
                'tradeRequests.seller.steamAccount',
                'supervisor',
            ])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));

                $query->where(function ($inner) use ($search) {
                    $inner->where('market_hash_name', 'like', "%{$search}%")
                        ->orWhere('item_name', 'like', "%{$search}%")
                        ->orWhere('steam_asset_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.marketplace.listings', [
            'listings' => $listings,
        ]);
    }

    public function cancelListing(Request $request, SkinListing $listing): RedirectResponse
    {
        if (! in_array($listing->status, ['draft', 'active', 'pending'], true)) {
            return back()->with('error', 'That listing cannot be cancelled.');
        }

        DB::transaction(function () use ($request, $listing): void {
            $oldListingStatus = $listing->status;

            $listing->update([
                'status' => 'cancelled',
            ]);

            $openTradeRequests = TradeRequest::where('skin_listing_id', $listing->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->get();

            foreach ($openTradeRequests as $tradeRequest) {
                $oldTradeStatus = $tradeRequest->status;

                $tradeRequest->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                TradeRequestEvent::create([
                    'trade_request_id' => $tradeRequest->id,
                    'actor_user_id' => $request->user()->id,
                    'event_type' => 'cancelled_by_admin',
                    'old_status' => $oldTradeStatus,
                    'new_status' => 'cancelled',
                    'metadata' => [
                        'listing_id' => $listing->id,
                        'old_listing_status' => $oldListingStatus,
                        'new_listing_status' => 'cancelled',
                    ],
                ]);
            }
        });

        return back()->with('success', 'Listing cancelled.');
    }

    public function tradeRequests(Request $request): View
    {
        $tradeRequests = TradeRequest::query()
            ->with([
                'listing.user.steamAccount',
                'buyer.steamAccount',
                'seller.steamAccount',
                'events.actor',
            ])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.marketplace.trade-requests', [
            'tradeRequests' => $tradeRequests,
        ]);
    }

    public function suspendUser(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Admin users cannot be suspended from here.');
        }

        DB::transaction(function () use ($user): void {
            $user->update([
                'status' => 'suspended',
            ]);

            SkinListing::where('user_id', $user->id)
                ->whereIn('status', ['draft', 'active', 'pending'])
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            TradeRequest::where(function ($query) use ($user) {
                $query->where('buyer_user_id', $user->id)
                    ->orWhere('seller_user_id', $user->id);
            })
                ->whereIn('status', ['pending', 'accepted'])
                ->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return back()->with('success', 'User suspended and open marketplace activity cancelled.');
    }

    public function restoreUser(User $user): RedirectResponse
    {
        $user->update([
            'status' => 'active',
        ]);

        return back()->with('success', 'User restored.');
    }
}