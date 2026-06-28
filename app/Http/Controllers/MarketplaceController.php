<?php

namespace App\Http\Controllers;

use App\Models\SkinListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = SkinListing::query()
            ->with(['user.steamAccount'])
            ->where('status', 'active');

        if ($request->filled('search')) {
            $search = trim($request->string('search'));

            $query->where(function ($inner) use ($search) {
                $inner->where('market_hash_name', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('weapon_type', 'like', "%{$search}%")
                    ->orWhere('rarity', 'like', "%{$search}%")
                    ->orWhere('wear_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->string('listing_type'));
        }

        if ($request->filled('rarity')) {
            $query->where('rarity', $request->string('rarity'));
        }

        if ($request->filled('wear_name')) {
            $query->where('wear_name', $request->string('wear_name'));
        }

        match ($request->string('sort')->toString()) {
            'price_low' => $query->orderByRaw('asking_price_cents IS NULL, asking_price_cents ASC'),
            'price_high' => $query->orderByRaw('asking_price_cents IS NULL, asking_price_cents DESC'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $listings = $query->paginate(24)->withQueryString();

        return view('marketplace.index', [
            'listings' => $listings,
            'rarities' => SkinListing::query()
                ->where('status', 'active')
                ->whereNotNull('rarity')
                ->distinct()
                ->orderBy('rarity')
                ->pluck('rarity'),
            'wears' => SkinListing::query()
                ->where('status', 'active')
                ->whereNotNull('wear_name')
                ->distinct()
                ->orderBy('wear_name')
                ->pluck('wear_name'),
        ]);
    }

    public function show(SkinListing $listing): View
    {
        abort_unless($listing->status === 'active', 404);

        $listing->load(['user.steamAccount','supervisor']);

        return view('marketplace.show', [
            'listing' => $listing,
        ]);
    }
}