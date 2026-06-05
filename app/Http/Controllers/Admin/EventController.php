<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->withCount(['stages', 'matches', 'pickemRecommendations'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            ->orderByDesc('starts_on')
            ->paginate(25);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $event = new Event([
            'status' => 'upcoming',
            'has_pickem' => false,
            'is_featured' => false,
        ]);

        return view('admin.events.create', compact('event'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['has_pickem'] = $request->boolean('has_pickem');
        $data['is_featured'] = $request->boolean('is_featured');

        Event::create($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validatedData($request, $event);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['has_pickem'] = $request->boolean('has_pickem');
        $data['is_featured'] = $request->boolean('is_featured');

        $event->update($data);

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }

    private function validatedData(Request $request, ?Event $event = null): array
    {
        $eventId = $event?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:events,slug,' . $eventId],
            'organizer' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', 'in:upcoming,live,completed,cancelled'],
            'summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
