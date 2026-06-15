<?php

namespace App\Jobs\Grid;

use App\Models\Event;
use App\Models\EventStage;
use App\Models\GridImportRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class ImportGridTournamentStatsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

            $scope = (string) data_get($run->input, 'scope', 'tournament_to_date');

            $gridTournamentId = $stage?->grid_id ?: $event->grid_id;

            if (! $gridTournamentId) {
                throw new RuntimeException('Selected event/stage does not have a GRID tournament ID.');
            }

            $endpoint = config('services.grid.stats_feed_endpoint');
            $apiKey = config('services.grid.api_key');
            $titleId = config('services.grid.cs2_title_id', '28');

            if (! $endpoint) {
                throw new RuntimeException('GRID Stats Feed endpoint is missing.');
            }

            if (! $apiKey) {
                throw new RuntimeException('GRID API key is missing.');
            }

            $includeChildren = $stage === null;

            $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                ])
                ->acceptJson()
                ->asJson()
                ->timeout(45)
                ->post($endpoint, [
                    'query' => $this->query(),
                    'variables' => [
                        'titleId' => (string) $titleId,
                        'tournamentIds' => [(string) $gridTournamentId],
                        'includeChildren' => $includeChildren,
                    ],
                ]);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'GRID Stats Feed request failed: '
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

            $stats = data_get($json, 'data.seriesStatistics', []);

            $run->update([
                'status' => 'completed',
                'output' => [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'event_stage_id' => $stage?->id,
                    'stage_name' => $stage?->name,
                    'scope' => $scope,
                    'grid_title_id' => (string) $titleId,
                    'grid_tournament_id' => (string) $gridTournamentId,
                    'include_children' => $includeChildren,
                    'note' => 'Tournament aggregate imported into this run output. Team/player snapshots require mapped GRID team/player IDs.',
                    'stats' => $stats,
                ],
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            $run->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            throw $e;
        }
    }

    private function query(): string
    {
        return <<<'GRAPHQL'
query ImportTournamentAggregateStats($titleId: ID!, $tournamentIds: [ID!], $includeChildren: Boolean!) {
  seriesStatistics(
    titleId: $titleId
    filter: {
      tournament: {
        id: { in: $tournamentIds }
        includeChildren: $includeChildren
      }
    }
  ) {
    count
    aggregationSeriesIds
  }
}
GRAPHQL;
    }
}