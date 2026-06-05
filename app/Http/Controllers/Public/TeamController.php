<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()
            ->withCount('players')
            ->where('status', 'active')
            ->orderByDesc('picklab_rating')
            ->paginate(24);

        return view('public.teams.index', compact('teams'));
    }

    public function show(Team $team): View
    {
        $team->load([
            'players',
            'pickemRecommendations.event',
            'pickemRecommendations.stage',
        ]);

        $recentMatches = $team->matchesAsTeamOne()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction'])
            ->latest('starts_at')
            ->take(5)
            ->get()
            ->merge(
                $team->matchesAsTeamTwo()
                    ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction'])
                    ->latest('starts_at')
                    ->take(5)
                    ->get()
            )
            ->sortByDesc('starts_at')
            ->take(5);

        return view('public.teams.show', compact('team', 'recentMatches'));
    }
}
