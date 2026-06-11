<?php

namespace App\Http\Controllers;

use App\Models\SkinListing;
use App\Models\SteamInventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkinListingController extends Controller
{
    public function create(Request $request): View
    {
        $listedAssetIds = SkinListing::where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'active', 'pending'])
            ->pluck('steam_asset_id')
            ->filter()
            ->values();

        $items = SteamInventoryItem::where('user_id', $request->user()->id)
            ->where('tradable', true)
            ->whereNotIn('asset_id', $listedAssetIds)
            ->orderBy('market_hash_name')
            ->paginate(24);

        return view('marketplace.listings.create', [
            'items' => $items,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'string'],
            'listing_type' => ['required', 'in:trade,sale'],
            'asking_price' => ['nullable', 'numeric', 'min:0'],
        ]);

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

        if (($validated['listing_type'] ?? 'trade') === 'sale' && $request->filled('asking_price')) {
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
            ->route('marketplace.index')
            ->with('success', 'Listing created.');
    }
}