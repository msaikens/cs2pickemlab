<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Grid\DiscoverGridSeriesJob;
use App\Jobs\Grid\DownloadGridSeriesFilesJob;
use App\Jobs\Grid\ImportGridTournamentStatsJob;
use App\Jobs\Grid\SearchGridTournamentsJob;
use App\Models\Event;
use App\Models\GridImportRun;
use App\Models\GridSeries;
use App\Models\GridTournamentCache;
use App\Models\EventStage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GridImportController extends Controller
{
    public function index()
    {
        return view('admin.grid.index', [
            'events' => Event::query()
                ->orderByDesc('id')
                ->get(),

            'runs' => GridImportRun::query()
                ->with(['event', 'stage', 'user'])
                ->latest()
                ->limit(25)
                ->get(),

            'series' => GridSeries::query()
                ->with(['event', 'stage'])
                ->latest()
                ->limit(50)
                ->get(),

            'gridTournamentResults' => GridTournamentCache::query()
                ->orderByDesc('last_seen_at')
                ->orderByDesc('id')
                ->limit(250)
                ->get(),
        ]);
    }

    public function searchTournaments(Request $request)
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $run = GridImportRun::create([
            'user_id' => $request->user()?->id,
            'action' => 'search_tournaments',
            'status' => 'queued',
            'input' => [
                'search' => trim((string) ($data['search'] ?? '')),
            ],
        ]);

        SearchGridTournamentsJob::dispatch($run->id);

        return redirect()
            ->route('admin.grid.index')
            ->with('status', 'GRID tournament search queued.');
    }

    public function createEventFromTournament(Request $request)
    {
        $data = $request->validate([
            'grid_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $now = now();

        $values = [
            'grid_id' => $data['grid_id'],
            'name' => $data['name'],
        ];

        if (Schema::hasColumn('events', 'slug')) {
            $values['slug'] = Str::slug($data['name']);
        }

        if (Schema::hasColumn('events', 'status')) {
            $values['status'] = 'draft';
        }

        if (Schema::hasColumn('events', 'title_id')) {
            $values['title_id'] = config('services.grid.cs2_title_id', '28');
        }

        if (Schema::hasColumn('events', 'game_title_id')) {
            $values['game_title_id'] = config('services.grid.cs2_title_id', '28');
        }

        if (Schema::hasColumn('events', 'updated_at')) {
            $values['updated_at'] = $now;
        }

        $existing = DB::table('events')
            ->where('grid_id', $data['grid_id'])
            ->first();

        if ($existing) {
            DB::table('events')
                ->where('id', $existing->id)
                ->update($values);
        } else {
            if (Schema::hasColumn('events', 'created_at')) {
                $values['created_at'] = $now;
            }

            DB::table('events')->insert($values);
        }

        return redirect()
            ->route('admin.grid.index')
            ->with('status', 'Local event created/linked from GRID tournament.');
    }

    public function dismissRunNotification(GridImportRun $run): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $output = $run->output ?? [];

        if (is_string($output)) {
            $output = json_decode($output, true) ?: [];
        }

        if (! is_array($output)) {
            $output = [];
        }

        $output['notification_dismissed_at'] = now()->toIso8601String();
        $output['notification_dismissed_by_user_id'] = auth()->id();

        $run->update([
            'output' => $output,
        ]);

        return back()->with('grid_status', "GRID warning for run #{$run->id} was dismissed.");
    }

    public function discoverSeries(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'event_stage_id' => ['nullable', 'exists:event_stages,id'],
        ]);

        $event = Event::findOrFail($data['event_id']);

        $run = GridImportRun::create([
            'user_id' => $request->user()?->id,
            'event_id' => $event->id,
            'event_stage_id' => $data['event_stage_id'] ?? null,
            'action' => 'discover_series',
            'status' => 'queued',
            'input' => $data,
        ]);

        DiscoverGridSeriesJob::dispatch($run->id);

        return redirect()
            ->route('admin.grid.index')
            ->with('status', 'GRID series discovery queued.');
    }

    public function downloadSeriesFiles(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'event_stage_id' => ['nullable', 'exists:event_stages,id'],
        ]);

        $run = GridImportRun::create([
            'user_id' => $request->user()?->id,
            'event_id' => $data['event_id'],
            'event_stage_id' => $data['event_stage_id'] ?? null,
            'action' => 'download_series_files',
            'status' => 'queued',
            'input' => $data,
        ]);

        DownloadGridSeriesFilesJob::dispatch($run->id);

        return redirect()
            ->route('admin.grid.index')
            ->with('status', 'GRID series file downloads queued.');
    }

    public function importStats(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'event_stage_id' => ['nullable', 'exists:event_stages,id'],
            'scope' => ['required', 'string', 'max:60'],
        ]);

        $run = GridImportRun::create([
            'user_id' => $request->user()?->id,
            'event_id' => $data['event_id'],
            'event_stage_id' => $data['event_stage_id'] ?? null,
            'action' => 'import_stats',
            'status' => 'queued',
            'input' => $data,
        ]);

        ImportGridTournamentStatsJob::dispatch($run->id);

        return redirect()
            ->route('admin.grid.index')
            ->with('status', 'GRID stats import queued.');
    }
    public function clearTournamentCache(): RedirectResponse
{
    abort_unless(auth()->user()?->isAdmin(), 403);

    $deleted = GridTournamentCache::query()->delete();

    return redirect()
        ->route('admin.grid.index')
        ->with('grid_status', "Cleared {$deleted} cached GRID tournament row(s). Local events were not deleted.");
}

public function clearSeriesDiscoveries(): RedirectResponse
{
    abort_unless(auth()->user()?->isAdmin(), 403);

    $deleted = GridSeries::query()->delete();

    return redirect()
        ->route('admin.grid.index')
        ->with('grid_status', "Cleared {$deleted} discovered GRID series row(s). Local events were not deleted.");
}

public function deleteLocalEvent(Event $event): RedirectResponse
{
    abort_unless(auth()->user()?->isAdmin(), 403);

    try {
        $eventId = $event->id;
        $eventName = $event->name;

        $deleted = DB::transaction(function () use ($event, $eventId) {
            $seriesDeleted = GridSeries::query()
                ->where('event_id', $eventId)
                ->delete();

            $stagesDeleted = EventStage::query()
                ->where('event_id', $eventId)
                ->delete();

            $runsDetached = GridImportRun::query()
                ->where('event_id', $eventId)
                ->update([
                    'event_id' => null,
                ]);

            $event->delete();

            return [
                'series_deleted' => $seriesDeleted,
                'stages_deleted' => $stagesDeleted,
                'runs_detached' => $runsDetached,
            ];
        });

        return redirect()
            ->route('admin.grid.index')
            ->with(
                'grid_status',
                "Deleted local event #{$eventId} ({$eventName}). Removed {$deleted['series_deleted']} GRID series row(s), {$deleted['stages_deleted']} stage row(s), and detached {$deleted['runs_detached']} import run(s)."
            );
    } catch (Throwable $e) {
        AdminReporter::report($e, 'GRID local event deletion failed', [
            'event_id' => $event->id ?? null,
            'event_name' => $event->name ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.grid.index')
            ->with(
                'grid_warning',
                'The local event could not be deleted safely. The issue was reported for admin review.'
            );
    }
}
}