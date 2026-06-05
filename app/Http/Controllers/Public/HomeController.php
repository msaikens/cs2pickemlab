<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Matches;
use App\Models\PickemRecommendation;
use App\Models\Product;
use App\Models\Prediction;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredEvent = Event::query()
            ->where('is_featured', true)
            ->latest()
            ->first();

        $upcomingMatches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction.predictedWinner'])
            ->whereIn('status', ['scheduled', 'live'])
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        $latestPredictions = Prediction::query()
            ->with(['match.teamOne', 'match.teamTwo', 'predictedWinner'])
            ->where('status', 'published')
            ->latest('published_at')
            ->take(4)
            ->get();

        $pickemRecommendations = PickemRecommendation::query()
            ->with(['event', 'stage', 'team'])
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('public.home', compact(
            'featuredEvent',
            'upcomingMatches',
            'latestPredictions',
            'pickemRecommendations',
            'featuredProducts'
        ));
    }
}
