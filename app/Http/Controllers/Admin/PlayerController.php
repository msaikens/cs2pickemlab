<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(): View
    {
        $players = Player::query()
            ->with('team')
            ->orderBy('handle')
            ->paginate(25);

        return view('admin.players.index', compact('players'));
    }

    public function create(): View
    {
        $player = new Player([
            'status' => 'active',
        ]);

        $teams = $this->teamOptions();

        return view('admin.players.create', compact('player', 'teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['slug'] = $data['slug'] ?: Str::slug($data['handle']);

        Player::create($data);

        return redirect()
            ->route('admin.players.index')
            ->with('success', 'Player created.');
    }

    public function edit(Player $player): View
    {
        $teams = $this->teamOptions();

        return view('admin.players.edit', compact('player', 'teams'));
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $data = $this->validatedData($request, $player);

        $data['slug'] = $data['slug'] ?: Str::slug($data['handle']);

        $player->update($data);

        return redirect()
            ->route('admin.players.edit', $player)
            ->with('success', 'Player updated.');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();

        return redirect()
            ->route('admin.players.index')
            ->with('success', 'Player deleted.');
    }

    private function validatedData(Request $request, ?Player $player = null): array
    {
        $playerId = $player?->id ?? 'NULL';

        return $request->validate([
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'handle' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:players,slug,' . $playerId],
            'real_name' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'in:awper,rifler,igl,support,lurker,entry,coach,substitute'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'kd_ratio' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'impact_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'status' => ['required', 'in:active,benched,inactive,retired'],
            'summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function teamOptions()
    {
        return Team::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }
}
