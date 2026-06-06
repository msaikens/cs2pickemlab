<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'teams' => DB::table('teams')->count(),
            'players' => DB::table('players')->count(),
            'events' => DB::table('events')->count(),
            'matches' => DB::table('matches')->count(),
            'predictions' => DB::table('predictions')->count(),
            'pickem' => DB::table('pickem_recommendations')->count(),
            'products' => DB::table('products')->count(),
            'orders' => DB::table('orders')->count(),
        ];

        $latestMatches = DB::table('matches')
            ->leftJoin('events', 'matches.event_id', '=', 'events.id')
            ->leftJoin('event_stages', 'matches.event_stage_id', '=', 'event_stages.id')
            ->leftJoin('teams as team_one', 'matches.team_one_id', '=', 'team_one.id')
            ->leftJoin('teams as team_two', 'matches.team_two_id', '=', 'team_two.id')
            ->select([
                'matches.id',
                'matches.status',
                'matches.format',
                'matches.starts_at',
                'matches.created_at',
                'events.name as event_name',
                'event_stages.name as stage_name',
                'team_one.name as team_one_name',
                'team_two.name as team_two_name',
            ])
            ->orderByDesc('matches.created_at')
            ->limit(5)
            ->get()
            ->map(fn ($match) => (array) $match)
            ->all();

        $latestProducts = DB::table('products')
            ->select([
                'id',
                'name',
                'slug',
                'status',
                'base_price',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($product) => (array) $product)
            ->all();

        $latestOrders = DB::table('orders')
            ->select([
                'id',
                'order_number',
                'customer_email',
                'status',
                'payment_status',
                'total',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($order) => (array) $order)
            ->all();

        return view('admin.dashboard', compact(
            'stats',
            'latestMatches',
            'latestProducts',
            'latestOrders'
        ));
    }
}
