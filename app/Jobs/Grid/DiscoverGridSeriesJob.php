<?php

namespace App\Jobs\Grid;

use App\Models\Event;
use App\Models\EventStage;
use App\Models\GridImportRun;
use App\Models\GridSeries;
use App\Support\AdminReporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class DiscoverGridSeriesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const GRID_MAX_PAGE_SIZE = 50;
    private const MAX_PAGES = 100;

    public int $timeout = 300;

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
            $event = Event::findOrFail((int) data_get($run->input, 'event_id'));

            $stage = null;

            if (data_get($run->input, 'event_stage_id')) {
                $stage = EventStage::findOrFail((int) data_get($run->input, 'event_stage_id'));
            }

            $gridTournamentId = $stage?->grid_id ?: $event->grid_id;

            if (! $gridTournamentId) {
                throw new RuntimeException('Selected event/stage does not have a GRID tournament ID.');
            }

            $endpoint = config('services.grid.central_data_endpoint');
            $apiKey = config('services.grid.api_key');
            $titleId = (string) config('services.grid.cs2_title_id', '28');

            if (! $endpoint) {
                throw new RuntimeException('GRID Central Data endpoint is missing.');
            }

            if (! $apiKey) {
                throw new RuntimeException('GRID API key is missing.');
            }

            if ($titleId === '') {
                throw new RuntimeException('GRID CS2 title ID is missing.');
            }

            $includeChildren = $stage === null;

            $result = $this->discoverSeries(
                endpoint: $endpoint,
                apiKey: $apiKey,
                gridTournamentId: (string) $gridTournamentId,
                titleId: $titleId,
                includeChildren: $includeChildren,
                event: $event,
                stage: $stage
            );

            if ($result['series_saved'] === 0) {
                $message = 'No GRID series were found for this event. Confirm the GRID tournament ID is correct, then try Discover Series again.';

                $run->update([
                    'status' => 'failed',
                    'error_message' => $message,
                    'output' => [
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                        'event_stage_id' => $stage?->id,
                        'stage_name' => $stage?->name,
                        'grid_tournament_id' => (string) $gridTournamentId,
                        'grid_title_id' => $titleId,
                        'include_children' => $includeChildren,
                        'series_count' => 0,
                        'series_saved' => 0,
                        'pages_checked' => $result['pages_checked'],
                        'warning' => $message,
                        'next_step' => 'Verify the linked GRID tournament ID, then run Discover Series IDs again.',
                        'note' => 'No series were returned from GRID Central Data allSeries.',
                    ],
                    'finished_at' => now(),
                ]);

                return;
            }

            $run->update([
                'status' => 'completed',
                'error_message' => null,
                'output' => [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'event_stage_id' => $stage?->id,
                    'stage_name' => $stage?->name,
                    'grid_tournament_id' => (string) $gridTournamentId,
                    'grid_title_id' => $titleId,
                    'include_children' => $includeChildren,
                    'series_count' => $result['series_count'],
                    'series_saved' => $result['series_saved'],
                    'pages_checked' => $result['pages_checked'],
                    'note' => 'Series IDs were discovered from GRID Central Data allSeries and saved to grid_series.',
                ],
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            AdminReporter::report($e, 'GRID series discovery failed', [
                'run_id' => $run->id ?? null,
                'event_id' => data_get($run->input, 'event_id'),
                'event_stage_id' => data_get($run->input, 'event_stage_id'),
                'action' => $run->action ?? null,
            ]);

            $run->update([
                'status' => 'failed',
                'error_message' => $this->friendlyErrorMessage($e),
                'output' => [
                    'event_id' => data_get($run->input, 'event_id'),
                    'event_stage_id' => data_get($run->input, 'event_stage_id'),
                    'admin_error' => $e->getMessage(),
                    'note' => 'The GRID series discovery failed safely. The admin page can continue to be used.',
                ],
                'finished_at' => now(),
            ]);

            return;
        }
    }

    private function discoverSeries(
        string $endpoint,
        string $apiKey,
        string $gridTournamentId,
        string $titleId,
        bool $includeChildren,
        Event $event,
        ?EventStage $stage
    ): array {
        $after = null;
        $page = 1;
        $seriesCount = 0;
        $seriesSaved = 0;

        do {
            $json = $this->postGraphql($endpoint, $apiKey, $this->query(), [
                'first' => self::GRID_MAX_PAGE_SIZE,
                'after' => $after,
                'tournamentIds' => [$gridTournamentId],
                'includeChildren' => $includeChildren,
            ]);

            $connection = data_get($json, 'data.allSeries', []);
            $edges = data_get($connection, 'edges', []);

            foreach ($edges as $edge) {
                $node = $edge['node'] ?? [];
                $seriesId = $node['id'] ?? null;

                if (! $seriesId) {
                    continue;
                }

                $teams = collect($node['teams'] ?? [])
                    ->map(function ($team) {
                        return [
                            'id' => data_get($team, 'baseInfo.id'),
                            'name' => data_get($team, 'baseInfo.name'),
                        ];
                    })
                    ->filter(fn ($team) => ! empty($team['id']) || ! empty($team['name']))
                    ->values();

                $teamOne = $teams->get(0, []);
                $teamTwo = $teams->get(1, []);

                GridSeries::updateOrCreate(
                    [
                        'grid_series_id' => (string) $seriesId,
                    ],
                    [
                        'event_id' => $event->id,
                        'event_stage_id' => $stage?->id,
                        'grid_tournament_id' => $gridTournamentId,
                        'grid_title_id' => $titleId,
                        'status' => 'discovered',
                        'team_one_name' => $teamOne['name'] ?? null,
                        'team_two_name' => $teamTwo['name'] ?? null,
                        'last_seen_at' => now(),
                        'source_payload' => [
                            'source' => 'central_data.allSeries',
                            'event_grid_id' => $event->grid_id,
                            'stage_grid_id' => $stage?->grid_id,
                            'include_children' => $includeChildren,
                            'start_time_scheduled' => data_get($node, 'startTimeScheduled'),
                            'teams' => $teams->all(),
                            'raw_node' => $node,
                        ],
                    ]
                );

                $seriesCount++;
                $seriesSaved++;
            }

            $hasNextPage = (bool) data_get($connection, 'pageInfo.hasNextPage', false);
            $after = data_get($connection, 'pageInfo.endCursor');

            $page++;

            sleep(1);
        } while ($hasNextPage && $after && $page <= self::MAX_PAGES);

        return [
            'series_count' => $seriesCount,
            'series_saved' => $seriesSaved,
            'pages_checked' => $page - 1,
        ];
    }

    private function postGraphql(string $endpoint, string $apiKey, string $query, array $variables): array
    {
        $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])
            ->acceptJson()
            ->asJson()
            ->timeout(45)
            ->post($endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'GRID Central Data request failed: '
                . $response->status()
                . ' '
                . $response->body()
            );
        }

        $json = $response->json();

        if (! empty($json['errors'])) {
            throw new RuntimeException(
                'GRID GraphQL errors: ' . json_encode($json['errors'], JSON_PRETTY_PRINT)
            );
        }

        return $json;
    }

    private function query(): string
{
    return <<<'GRAPHQL'
query DiscoverTournamentSeries($first: Int!, $after: String, $tournamentIds: [ID!], $includeChildren: Boolean!) {
  allSeries(
    first: $first
    after: $after
    filter: {
      tournament: {
        id: { in: $tournamentIds }
        includeChildren: { equals: $includeChildren }
      }
    }
  ) {
    pageInfo {
      hasNextPage
      endCursor
    }
    edges {
      node {
        id
        startTimeScheduled
        teams {
          baseInfo {
            id
            name
          }
        }
      }
    }
  }
}
GRAPHQL;
}

    private function friendlyErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Central Data endpoint')) {
            return 'GRID series discovery could not run because the Central Data endpoint is missing.';
        }

        if (str_contains($message, 'API key')) {
            return 'GRID series discovery could not run because the API key is missing or invalid.';
        }

        if (str_contains($message, '401') || str_contains($message, '403')) {
            return 'GRID rejected the Central Data request because the credentials or permissions were not accepted.';
        }

        if (str_contains($message, 'timed out') || str_contains($message, 'cURL error 28')) {
            return 'GRID Central Data did not respond before the request timed out.';
        }

        return 'GRID series discovery failed safely. Review the saved admin error details.';
    }
}