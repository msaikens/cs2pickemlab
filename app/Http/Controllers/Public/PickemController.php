<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PickemRecommendation;
use Illuminate\View\View;

class PickemController extends Controller
{
    public function index(): View
    {
        $event = Event::query()
            ->where('has_pickem', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->orderByDesc('is_featured')
            ->latest()
            ->first();

        $recommendations = PickemRecommendation::query()
            ->with(['event', 'stage', 'team'])
            ->where('status', 'published')
            ->when($event, fn ($query) => $query->where('event_id', $event->id))
            ->orderBy('slot_type')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('slot_type');

        return view('public.pickem.index', compact('event', 'recommendations'));
    }
}
