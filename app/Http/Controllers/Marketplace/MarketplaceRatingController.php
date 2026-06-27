<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\SkinListing;
use App\Models\TradeRequest;
use App\Models\User;
use App\Services\MarketplaceRatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarketplaceRatingController extends Controller
{
    public function store(
        Request $request,
        string $type,
        int $id,
        MarketplaceRatingService $ratings,
    ): RedirectResponse {
        $validated = $request->validate([
            'rated_user_id' => ['required', 'integer', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $rateable = match ($type) {
            'trade' => TradeRequest::query()->findOrFail($id),
            'sale', 'listing' => SkinListing::query()->findOrFail($id),
            default => abort(404),
        };

        $ratedUser = User::query()->findOrFail($validated['rated_user_id']);

        $ratings->createRating(
            rater: $request->user(),
            ratedUser: $ratedUser,
            rateable: $rateable,
            rating: (int) $validated['rating'],
            comment: $validated['comment'] ?? null,
        );

        return back()->with('status', 'Rating submitted.');
    }
}