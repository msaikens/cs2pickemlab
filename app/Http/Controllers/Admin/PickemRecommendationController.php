<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStage;
use App\Models\PickemRecommendation;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PickemRecommendationController extends Controller
{
    public function index(): View
    {
        $recommendations = PickemRecommendation::query()
            ->with(['event', 'stage', 'team'])
            ->orderByRaw("CASE WHEN status = 'published' THEN 0 WHEN status = 'draft' THEN 1 ELSE 2 END")
            ->orderBy('slot_type')
            ->orderBy('sort_order')
            ->paginate(25);

        return view('admin.pickem.index', compact('recommendations'));
    }

    public function create(): View
    {
        $recommendation = new PickemRecommendation([
            'slot_type' => 'safe_advancement',
            'risk_level' => 'medium',
            'confidence_score' => 50,
            'status' => 'draft',
            'is_premium' => false,
            'sort_order' => 0,
        ]);

        return view('admin.pickem.create', $this->formData($recommendation));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['event_stage_id'] = empty($data['event_stage_id']) ? null : $data['event_stage_id'];
        $data['is_premium'] = $request->boolean('is_premium');

        PickemRecommendation::create($data);

        return redirect()
            ->route('admin.pickem.index')
            ->with('success', 'Pick’em recommendation created.');
    }

    public function edit(PickemRecommendation $pickem): View
    {
        return view('admin.pickem.edit', $this->formData($pickem));
    }

    public function update(Request $request, PickemRecommendation $pickem): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['event_stage_id'] = empty($data['event_stage_id']) ? null : $data['event_stage_id'];
        $data['is_premium'] = $request->boolean('is_premium');

        $pickem->update($data);

        return redirect()
            ->route('admin.pickem.edit', $pickem)
            ->with('success', 'Pick’em recommendation updated.');
    }

    public function destroy(PickemRecommendation $pickem): RedirectResponse
    {
        $pickem->delete();

        return redirect()
            ->route('admin.pickem.index')
            ->with('success', 'Pick’em recommendation deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'event_stage_id' => ['nullable', 'integer', 'exists:event_stages,id'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'slot_type' => [
                'required',
                'in:safe_3_0,risky_3_0,safe_advancement,risky_advancement,likely_0_3,upset_watch,avoid',
            ],
            'risk_level' => ['required', 'in:low,medium,high'],
            'confidence_score' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,archived'],
            'sort_order' => ['nullable', 'integer'],
            'headline' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'reasoning' => ['nullable', 'string'],
        ]);
    }

    private function formData(PickemRecommendation $recommendation): array
    {
        $events = Event::query()
            ->where('has_pickem', true)
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
            ->orderByDesc('starts_on')
            ->get();

        $stages = EventStage::query()
            ->with('event')
            ->where('has_pickem', true)
            ->orderBy('event_id')
            ->orderBy('sort_order')
            ->get();

        $teams = Team::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return compact('recommendation', 'events', 'stages', 'teams');
    }
}
