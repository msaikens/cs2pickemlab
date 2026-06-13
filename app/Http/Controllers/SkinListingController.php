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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'string'],
            'listing_type' => ['required', 'in:trade,sale'],
            'asking_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['listing_type'] === 'sale' && ! config('marketplace.paid_sales_enabled')) {
            return back()
                ->with('error', 'Paid skin sales are not enabled yet. Use trade-only listings for now.')
                ->withInput();
        }

        $item = SteamInventoryItem::where('user_id', $request->user()->id)
            ->where('asset_id', $validated['asset_id'])
            ->where('tradable', true)
            ->firstOrFail();

        $alreadyListed = SkinListing::where('user_id', $request->user()->id)
            ->where('steam_asset_id', $item->asset_id)
            ->whereIn('status', ['draft', 'active', 'pending'])
            ->exists();

        if ($alreadyListed) {
            return back()
                ->with('error', 'That item is already listed.')
                ->withInput();
        }

        $askingPriceCents = null;

        if ($validated['listing_type'] === 'sale') {
            if (! $request->filled('asking_price')) {
                return back()
                    ->with('error', 'Sale listings require an asking price.')
                    ->withInput();
            }

            $askingPriceCents = (int) round(((float) $validated['asking_price']) * 100);
        }

        SkinListing::create([
            'user_id' => $request->user()->id,
            'steam_asset_id' => $item->asset_id,
            'market_hash_name' => $item->market_hash_name,
            'item_name' => $item->name ?? $item->market_hash_name,
            'weapon_type' => $item->type,
            'rarity' => $item->rarity,
            'wear_name' => $item->exterior,
            'float_value' => null,
            'image_url' => $item->image_url,
            'listing_type' => $validated['listing_type'],
            'asking_price_cents' => $askingPriceCents,
            'currency' => 'USD',
            'status' => 'active',
        ]);

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