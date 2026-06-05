<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use App\Models\Prediction;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(): View
    {
        $predictions = Prediction::query()
            ->with(['match.teamOne', 'match.teamTwo', 'predictedWinner'])
            ->orderByRaw("CASE WHEN status = 'published' THEN 0 WHEN status = 'draft' THEN 1 ELSE 2 END")
            ->latest('published_at')
            ->paginate(25);

        return view('admin.predictions.index', compact('predictions'));
    }

    public function create(): View
    {
        $prediction = new Prediction([
            'confidence_score' => 50,
            'upset_risk' => 'medium',
            'status' => 'draft',
            'is_premium' => false,
            'published_at' => now(),
        ]);

        return view('admin.predictions.create', $this->formData($prediction));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['predicted_winner_team_id'] = empty($data['predicted_winner_team_id']) ? null : $data['predicted_winner_team_id'];
        $data['best_pickem_use'] = empty($data['best_pickem_use']) ? null : $data['best_pickem_use'];
        $data['is_premium'] = $request->boolean('is_premium');

        Prediction::create($data);

        return redirect()
            ->route('admin.predictions.index')
            ->with('success', 'Prediction created.');
    }

    public function edit(Prediction $prediction): View
    {
        return view('admin.predictions.edit', $this->formData($prediction));
    }

    public function update(Request $request, Prediction $prediction): RedirectResponse
    {
        $data = $this->validatedData($request, $prediction);

        $data['predicted_winner_team_id'] = empty($data['predicted_winner_team_id']) ? null : $data['predicted_winner_team_id'];
        $data['best_pickem_use'] = empty($data['best_pickem_use']) ? null : $data['best_pickem_use'];
        $data['is_premium'] = $request->boolean('is_premium');

        $prediction->update($data);

        return redirect()
            ->route('admin.predictions.edit', $prediction)
            ->with('success', 'Prediction updated.');
    }

    public function destroy(Prediction $prediction): RedirectResponse
    {
        $prediction->delete();

        return redirect()
            ->route('admin.predictions.index')
            ->with('success', 'Prediction deleted.');
    }

    private function validatedData(Request $request, ?Prediction $prediction = null): array
    {
        $predictionId = $prediction?->id ?? 'NULL';

        return $request->validate([
            'match_id' => ['required', 'integer', 'exists:matches,id', 'unique:predictions,match_id,' . $predictionId],
            'predicted_winner_team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'confidence_score' => ['required', 'integer', 'min:0', 'max:100'],
            'upset_risk' => ['required', 'in:low,medium,high'],
            'best_pickem_use' => ['nullable', 'in:safe_3_0,risky_3_0,safe_advancement,risky_advancement,avoid,upset_watch'],
            'status' => ['required', 'in:draft,published,archived'],
            'headline' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'reasoning' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function formData(Prediction $prediction): array
    {
        $matches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'prediction'])
            ->orderBy('starts_at')
            ->get();

        $teams = Team::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return compact('prediction', 'matches', 'teams');
    }
}
