<?php

namespace App\Jobs\Grid;

use App\Models\Event;
use App\Models\GridImportRun;
use App\Models\GridSeries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class DownloadGridSeriesFilesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public int $runId
    ) {
    }

    public function handle(): void
    {
        $run = GridImportRun::findOrFail($this->runId);

        $run->update([
            'status' => 'running',
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $baseUrl = rtrim((string) config('services.grid.file_download_endpoint'), '/');
            $apiKey = config('services.grid.api_key');

            if (! $baseUrl) {
                throw new RuntimeException('GRID File Download endpoint is missing.');
            }

            if (! $apiKey) {
                throw new RuntimeException('GRID API key is missing.');
            }

            $eventId = (int) data_get($run->input, 'event_id');
            $stageId = data_get($run->input, 'event_stage_id');

            $stageId = $stageId !== null && $stageId !== ''
                ? (int) $stageId
                : null;

            $event = Event::query()->find($eventId);

            $query = GridSeries::query()
                ->where('event_id', $eventId);

            if ($stageId) {
                $query->where('event_stage_id', $stageId);
            }

            $seriesItems = $query
                ->whereIn('status', ['discovered', 'files_ready', 'failed'])
                ->orderBy('id')
                ->get();

            if ($seriesItems->isEmpty()) {
                $message = 'No discovered GRID series were found for this event. Run Discover Series IDs first.';

                Log::warning($message, [
                    'run_id' => $run->id,
                    'event_id' => $eventId,
                    'event_name' => $event?->name,
                    'grid_tournament_id' => $event?->grid_id,
                    'event_stage_id' => $stageId,
                ]);

                $run->update([
                    'status' => 'failed',
                    'error_message' => $message,
                    'output' => [
                        'warning' => $message,
                        'event_id' => $eventId,
                        'event_name' => $event?->name,
                        'event_stage_id' => $stageId,
                        'grid_tournament_id' => $event?->grid_id,
                        'grid_title_id' => (string) config('services.grid.cs2_title_id'),
                        'series_checked' => 0,
                        'downloaded_count' => 0,
                        'skipped_count' => 0,
                        'failed_count' => 0,
                        'downloaded' => [],
                        'skipped' => [],
                        'failed' => [],
                        'next_step' => 'Run Discover Series IDs for this event, then run Download Files again.',
                    ],
                    'finished_at' => now(),
                ]);

                return;
            }

            $downloaded = [];
            $skipped = [];
            $failed = [];

            foreach ($seriesItems as $series) {
                try {
                    $listResponse = Http::withHeaders([
                            'x-api-key' => $apiKey,
                        ])
                        ->acceptJson()
                        ->timeout(45)
                        ->get($baseUrl . '/list/' . $series->grid_series_id);

                    if (! $listResponse->successful()) {
                        throw new RuntimeException(
                            "File list failed for series {$series->grid_series_id}: "
                            . $listResponse->status()
                            . ' '
                            . $listResponse->body()
                        );
                    }

                    $files = $listResponse->json('files') ?? $listResponse->json() ?? [];

                    $eventsReady = collect($files)->contains(function ($file) {
                        return ($file['id'] ?? null) === 'events-grid-compressed'
                            && ($file['status'] ?? null) === 'ready';
                    });

                    $stateReady = collect($files)->contains(function ($file) {
                        return ($file['id'] ?? null) === 'state-grid'
                            && ($file['status'] ?? null) === 'ready';
                    });

                    if (! $eventsReady && ! $stateReady) {
                        $series->update([
                            'status' => 'files_ready',
                            'source_payload' => [
                                'file_list' => $files,
                                'note' => 'Files listed, but expected GRID files are not ready yet.',
                            ],
                            'last_seen_at' => now(),
                        ]);

                        $skipped[] = [
                            'series_id' => $series->grid_series_id,
                            'reason' => 'No ready GRID event/end-state files yet.',
                        ];

                        sleep(1);
                        continue;
                    }

                    $eventsPath = $series->events_file_path;
                    $statePath = $series->end_state_file_path;

                    if ($eventsReady) {
                        $eventsPath = "grid/series/{$series->grid_series_id}/events-grid.zip";

                        $downloadResponse = Http::withHeaders([
                                'x-api-key' => $apiKey,
                            ])
                            ->timeout(120)
                            ->get($baseUrl . '/events/grid/series/' . $series->grid_series_id);

                        if (! $downloadResponse->successful()) {
                            throw new RuntimeException(
                                "Events download failed for series {$series->grid_series_id}: "
                                . $downloadResponse->status()
                                . ' '
                                . $downloadResponse->body()
                            );
                        }

                        Storage::put($eventsPath, $downloadResponse->body());
                    }

                    if ($stateReady) {
                        $statePath = "grid/series/{$series->grid_series_id}/end-state-grid.json";

                        $downloadResponse = Http::withHeaders([
                                'x-api-key' => $apiKey,
                            ])
                            ->timeout(120)
                            ->get($baseUrl . '/end-state/grid/series/' . $series->grid_series_id);

                        if (! $downloadResponse->successful()) {
                            throw new RuntimeException(
                                "End-state download failed for series {$series->grid_series_id}: "
                                . $downloadResponse->status()
                                . ' '
                                . $downloadResponse->body()
                            );
                        }

                        Storage::put($statePath, $downloadResponse->body());
                    }

                    $series->update([
                        'status' => 'downloaded',
                        'events_file_path' => $eventsPath,
                        'end_state_file_path' => $statePath,
                        'source_payload' => [
                            'file_list' => $files,
                            'events_ready' => $eventsReady,
                            'state_ready' => $stateReady,
                        ],
                        'downloaded_at' => now(),
                        'last_seen_at' => now(),
                    ]);

                    $downloaded[] = [
                        'series_id' => $series->grid_series_id,
                        'events_file_path' => $eventsPath,
                        'end_state_file_path' => $statePath,
                    ];

                    sleep(1);
                } catch (Throwable $e) {
                    report($e);

                    Log::warning('GRID series file download failed safely.', [
                        'run_id' => $run->id,
                        'event_id' => $eventId,
                        'event_name' => $event?->name,
                        'grid_tournament_id' => $event?->grid_id,
                        'series_id' => $series->grid_series_id,
                        'message' => $e->getMessage(),
                    ]);

                    $series->update([
                        'status' => 'failed',
                        'source_payload' => [
                            'error' => $e->getMessage(),
                        ],
                        'last_seen_at' => now(),
                    ]);

                    $failed[] = [
                        'series_id' => $series->grid_series_id,
                        'error' => $e->getMessage(),
                    ];

                    sleep(1);
                }
            }

            $hasFailures = count($failed) > 0;

            $run->update([
                'status' => $hasFailures ? 'failed' : 'completed',
                'error_message' => $hasFailures
                    ? 'Some GRID series files failed to download. The job failed safely and the admin can keep working.'
                    : null,
                'output' => [
                    'event_id' => $eventId,
                    'event_name' => $event?->name,
                    'event_stage_id' => $stageId,
                    'grid_tournament_id' => $event?->grid_id,
                    'grid_title_id' => (string) config('services.grid.cs2_title_id'),
                    'series_checked' => $seriesItems->count(),
                    'downloaded_count' => count($downloaded),
                    'skipped_count' => count($skipped),
                    'failed_count' => count($failed),
                    'downloaded' => $downloaded,
                    'skipped' => $skipped,
                    'failed' => $failed,
                    'warning' => $hasFailures
                        ? 'One or more series failed to download. Check the failed list below.'
                        : null,
                ],
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);

            Log::error('GRID download job failed safely.', [
                'run_id' => $run->id ?? null,
                'message' => $e->getMessage(),
            ]);

            $run->update([
                'status' => 'failed',
                'error_message' => $this->friendlyErrorMessage($e),
                'output' => [
                    'admin_error' => $e->getMessage(),
                    'note' => 'The GRID download job failed safely. The admin page can continue to be used.',
                    'series_checked' => 0,
                    'downloaded_count' => 0,
                    'skipped_count' => 0,
                    'failed_count' => 0,
                    'downloaded' => [],
                    'skipped' => [],
                    'failed' => [],
                ],
                'finished_at' => now(),
            ]);

            return;
        }
    }

    private function friendlyErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'endpoint')) {
            return 'GRID file download could not run because the file download endpoint is missing.';
        }

        if (str_contains($message, 'API key')) {
            return 'GRID file download could not run because the API key is missing or invalid.';
        }

        if (str_contains($message, 'timed out') || str_contains($message, 'cURL error 28')) {
            return 'GRID file download timed out. Try again later.';
        }

        if (str_contains($message, '401') || str_contains($message, '403')) {
            return 'GRID rejected the file download request because credentials or permissions were not accepted.';
        }

        return 'GRID file download failed safely. An admin can review the saved error details.';
    }
}