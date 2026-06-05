<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()
            ->withCount('players')
            ->orderByDesc('picklab_rating')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.teams.index', compact('teams'));
    }

    public function create(): View
    {
        $team = new Team([
            'status' => 'active',
            'picklab_rating' => 1500,
        ]);

        return view('admin.teams.create', compact('team'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        Team::create($data);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team created.');
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $this->validatedData($request, $team);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $team->update($data);

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', 'Team updated.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team deleted.');
    }

    private function validatedData(Request $request, ?Team $team = null): array
    {
        $teamId = $team?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:teams,slug,' . $teamId],
            'short_name' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'picklab_rating' => ['required', 'integer', 'min:0', 'max:3000'],
            'status' => ['required', 'in:active,inactive,archived'],
            'summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
