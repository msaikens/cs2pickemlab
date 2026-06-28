<?php

namespace App\Http\Controllers;

use App\Models\SkinListing;
use App\Models\SteamInventoryItem;
use App\Models\TradeRequest;
use App\Models\TradeRequestEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SkinListingController extends Controller
{
    public function index(Request $request): View
    {
        $listings = SkinListing::query()
            ->with([
                'tradeRequests.buyer.steamAccount',
                'tradeRequests.seller.steamAccount',
                'supervisor',
            ])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('marketplace.listings.index', [
            'listings' => $listings,
        ]);
    }

    public function create(Request $request): View
{
    $user = $request->user();

    $alreadyListedAssetIds = SkinListing::query()
        ->where('user_id', $user->id)
        ->whereIn('status', ['draft', 'active', 'pending'])
        ->pluck('steam_asset_id')
        ->filter()
        ->values();

    $inventoryItems = $user->steamInventoryItems()
        ->where('tradable', true)
        ->when($alreadyListedAssetIds->isNotEmpty(), function ($query) use ($alreadyListedAssetIds) {
            $query->whereNotIn('asset_id', $alreadyListedAssetIds);
        })
        ->orderBy('market_hash_name')
        ->paginate(24)
        ->withQueryString();

    return view('marketplace.listings.create', [
        'inventoryItems' => $inventoryItems,
    ]);
}

public function store(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
{
    $validated = $request->validate([
        'asset_id' => ['required', 'string', 'max:191'],
        'listing_type' => ['required', 'string', 'in:trade,sale'],
        'asking_price_cents' => ['nullable', 'integer', 'min:100'],
    ]);

    $inventoryItem = \App\Models\SteamInventoryItem::query()
        ->where('user_id', $request->user()->id)
        ->where('asset_id', $validated['asset_id'])
        ->where('tradable', true)
        ->firstOrFail();

    $alreadyListed = SkinListing::query()
        ->where('user_id', $request->user()->id)
        ->where('steam_asset_id', $inventoryItem->asset_id)
        ->whereIn('status', ['draft', 'active', 'pending'])
        ->exists();

    if ($alreadyListed) {
        return back()
            ->withInput()
            ->with('error', 'This item is already listed.');
    }

    $listing = SkinListing::create([
        'user_id' => $request->user()->id,
        'steam_asset_id' => $inventoryItem->asset_id,
        'market_hash_name' => $inventoryItem->market_hash_name,
        'item_name' => $inventoryItem->name,
        'weapon_type' => $inventoryItem->weapon_type,
        'rarity' => $inventoryItem->rarity,
        'wear_name' => $inventoryItem->wear_name,
        'float_value' => $inventoryItem->float_value,
        'image_url' => $inventoryItem->image_url,
        'listing_type' => $validated['listing_type'],
        'asking_price_cents' => $validated['listing_type'] === 'sale'
            ? ($validated['asking_price_cents'] ?? null)
            : null,
        'currency' => 'USD',
        'status' => 'active',
    ]);

    if (class_exists(\App\Services\MarketplaceSupervisorService::class)) {
        app(\App\Services\MarketplaceSupervisorService::class)
            ->assignSupervisor($listing);
    }

    return redirect()
        ->route('marketplace.listings.index')
        ->with('success', 'Listing created.');
}

    public function cancel(Request $request, SkinListing $listing): RedirectResponse
    {
        abort_unless($listing->user_id === $request->user()->id, 403);

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
                    'event_type' => 'cancelled_due_to_listing_cancelled',
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
}