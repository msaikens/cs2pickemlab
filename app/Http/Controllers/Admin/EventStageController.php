<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventStageController extends Controller
{
    public function index(Event $event): View
    {
        $event->load(['stages' => fn ($query) => $query->orderBy('sort_order')]);

        return view('admin.events.stages.index', compact('event'));
    }

    public function create(Event $event): View
    {
        $stage = new EventStage([
            'event_id' => $event->id,
            'format' => 'swiss',
            'has_pickem' => false,
            'sort_order' => $event->stages()->count() + 1,
        ]);

        return view('admin.events.stages.create', compact('event', 'stage'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validatedData($request, $event);

        $data['event_id'] = $event->id;
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['has_pickem'] = $request->boolean('has_pickem');

        EventStage::create($data);

        return redirect()
            ->route('admin.events.stages.index', $event)
            ->with('success', 'Event stage created.');
    }

    public function edit(Event $event, EventStage $stage): View
    {
        $this->ensureStageBelongsToEvent($event, $stage);

        return view('admin.events.stages.edit', compact('event', 'stage'));
    }

    public function update(Request $request, Event $event, EventStage $stage): RedirectResponse
    {
        $this->ensureStageBelongsToEvent($event, $stage);

        $data = $this->validatedData($request, $event, $stage);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['has_pickem'] = $request->boolean('has_pickem');

        $stage->update($data);

        return redirect()
            ->route('admin.events.stages.edit', [$event, $stage])
            ->with('success', 'Event stage updated.');
    }

    public function destroy(Event $event, EventStage $stage): RedirectResponse
    {
        $this->ensureStageBelongsToEvent($event, $stage);

        $stage->delete();

        return redirect()
            ->route('admin.events.stages.index', $event)
            ->with('success', 'Event stage deleted.');
    }

    private function validatedData(Request $request, Event $event, ?EventStage $stage = null): array
    {
        $stageId = $stage?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:event_stages,slug,' . $stageId . ',id,event_id,' . $event->id,
            ],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'format' => ['nullable', 'in:swiss,groups,playoffs,single_elim,double_elim,round_robin,other'],
            'sort_order' => ['nullable', 'integer'],
            'summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function ensureStageBelongsToEvent(Event $event, EventStage $stage): void
    {
        abort_unless((int) $stage->event_id === (int) $event->id, 404);
    }
}
