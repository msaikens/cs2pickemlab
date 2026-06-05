<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(): View
    {
        $matches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction.predictedWinner'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'scheduled' THEN 1 ELSE 2 END")
            ->orderBy('starts_at')
            ->paginate(12);

        return view('public.matches.index', compact('matches'));
    }

    public function show(Matches $match): View
    {
        $match->load([
            'event',
            'stage',
            'teamOne.players',
            'teamTwo.players',
            'winner',
            'prediction.predictedWinner',
        ]);

        return view('public.matches.show', compact('match'));
    }
}
