<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Matches;
use App\Models\Order;
use App\Models\PickemRecommendation;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Product;
use App\Models\Team;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'teams' => Team::count(),
            'players' => Player::count(),
            'events' => Event::count(),
            'matches' => Matches::count(),
            'predictions' => Prediction::count(),
            'pickem' => PickemRecommendation::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
        ];

        $latestMatches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction'])
            ->latest()
            ->take(5)
            ->get();

        $latestProducts = Product::query()
            ->latest()
            ->take(5)
            ->get();

        $latestOrders = Order::query()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestMatches',
            'latestProducts',
            'latestOrders'
        ));
    }
}
