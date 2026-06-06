<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStage;
use App\Models\Matches;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(): View
    {
        $matches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'winner', 'prediction'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'scheduled' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            ->orderBy('starts_at')
            ->paginate(25);

        return view('admin.matches.index', compact('matches'));
    }

    public function create(): View
    {
        $match = new Matches([
            'status' => 'scheduled',
            'format' => 'bo3',
            'bracket_position' => 0,
        ]);

        return view('admin.matches.create', $this->formData($match));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data = $this->normalizeNullableFields($data);

        Matches::create($data);

        return redirect()
            ->route('admin.matches.index')
            ->with('success', 'Match created.');
    }

    public function edit(Matches $match): View
    {
        return view('admin.matches.edit', $this->formData($match));
    }

    public function update(Request $request, Matches $match): RedirectResponse
    {
        $data = $this->validatedData($request, $match);

        $data = $this->normalizeNullableFields($data);

        $match->update($data);

        return redirect()
            ->route('admin.matches.edit', $match)
            ->with('success', 'Match updated.');
    }

    public function destroy(Matches $match): RedirectResponse
    {
        $match->delete();

        return redirect()
            ->route('admin.matches.index')
            ->with('success', 'Match deleted.');
    }

    private function validatedData(Request $request, ?Matches $match = null): array
    {
        return $request->validate([
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'event_stage_id' => ['nullable', 'integer', 'exists:event_stages,id'],
            'team_one_id' => ['required', 'integer', 'exists:teams,id', 'different:team_two_id'],
            'team_two_id' => ['required', 'integer', 'exists:teams,id'],
            'winner_team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'starts_at' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,live,completed,postponed,cancelled'],
            'format' => ['required', 'in:bo1,bo3,bo5'],

            'bracket_group' => ['nullable', 'in:swiss,playoffs'],
            'round_label' => ['nullable', 'string', 'max:100'],
            'bracket_position' => ['nullable', 'integer', 'min:0'],

            'team_one_score' => ['nullable', 'integer', 'min:0', 'max:5'],
            'team_two_score' => ['nullable', 'integer', 'min:0', 'max:5'],
            'summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function normalizeNullableFields(array $data): array
    {
        foreach ([
            'event_id',
            'event_stage_id',
            'winner_team_id',
            'bracket_group',
            'round_label',
        ] as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $data['bracket_position'] = isset($data['bracket_position'])
            ? (int) $data['bracket_position']
            : 0;

        return $data;
    }

    private function formData(Matches $match): array
    {
        $events = Event::query()
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
            ->orderByDesc('starts_on')
            ->get();

        $stages = EventStage::query()
            ->with('event')
            ->orderBy('event_id')
            ->orderBy('sort_order')
            ->get();

        $teams = Team::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return compact('match', 'events', 'stages', 'teams');
    }
}
