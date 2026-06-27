<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MarketplaceProfileAccessService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function show(
        Request $request,
        User $user,
        MarketplaceProfileAccessService $access,
    ): View {
        abort_unless($access->canViewProfile($request->user(), $user), 403);

        $user->loadMissing(['profile', 'steamAccount']);

        $ratings = $user->ratingsReceived()
            ->with('rater')
            ->latest()
            ->limit(10)
            ->get();

        return view('marketplace.users.show', [
            'profileUser' => $user,
            'ratings' => $ratings,
            'averageRating' => $user->averageMarketplaceRating(),
            'ratingCount' => $user->marketplaceRatingCount(),
            'viewer' => $request->user(),
        ]);
    }
}