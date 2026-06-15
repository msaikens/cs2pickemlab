<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GridHuntCs2Tournaments extends Command
{
    protected $signature = 'grid:hunt-cs2-tournaments
        {start : Starting GRID tournament ID}
        {end : Ending GRID tournament ID}
        {--title_id= : GRID CS2 title ID}
        {--delay=2 : Seconds to wait between requests}
        {--output=grid/discovery/cs2-tournament-hits.jsonl : Output JSONL file}';

    protected $description = 'Slowly probe GRID tournament IDs for CS2 series using Stats Feed.';

    public function handle(): int
    {
        $start = (int) $this->argument('start');
        $end = (int) $this->argument('end');
        $titleId = (string) $this->option('title_id');
        $delay = max(1, (int) $this->option('delay'));
        $output = (string) $this->option('output');

        $endpoint = config('services.grid.stats_feed_endpoint');
        $apiKey = config('services.grid.api_key');

        if (! $endpoint || ! $apiKey) {
            $this->error('Missing GRID stats endpoint or API key.');
            return self::FAILURE;
        }

        if ($titleId === '') {
            $this->error('Missing --title_id. You need the GRID CS2 title ID first.');
            return self::FAILURE;
        }

        if ($end < $start) {
            $this->error('End ID must be greater than or equal to start ID.');
            return self::FAILURE;
        }

        Storage::makeDirectory(dirname($output));

        $this->info("Scanning GRID tournament IDs {$start} through {$end}");
        $this->warn("Delay: {$delay}s/request. Stop this if GRID returns 403 or 429.");

        for ($id = $start; $id <= $end; $id++) {
            $this->line("Checking tournament {$id}...");

            $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                ])
                ->acceptJson()
                ->asJson()
                ->timeout(45)
                ->post($endpoint, [
                    'query' => $this->query(),
                    'variables' => [
                        'titleId' => $titleId,
                        'tournamentIds' => [(string) $id],
                    ],
                ]);

            if (in_array($response->status(), [401, 403, 429], true)) {
                $this->error("Stopping. GRID returned {$response->status()}: {$response->body()}");
                return self::FAILURE;
            }

            if (! $response->successful()) {
                $this->warn("Request failed for {$id}: {$response->status()}");
                sleep($delay);
                continue;
            }

            $json = $response->json();

            if (! empty($json['errors'])) {
                $this->warn("GraphQL error for {$id}: " . json_encode($json['errors']));
                sleep($delay);
                continue;
            }

            $stats = $json['data']['seriesStatistics'] ?? null;

            $count = (int) ($stats['count'] ?? 0);
            $seriesIds = $stats['aggregationSeriesIds'] ?? [];

            if ($count > 0 || count($seriesIds) > 0) {
                $hit = [
                    'tournament_id' => (string) $id,
                    'title_id' => $titleId,
                    'series_count' => $count,
                    'series_ids' => $seriesIds,
                    'found_at' => now()->toIso8601String(),
                ];

                Storage::append($output, json_encode($hit));

                $this->info("HIT tournament {$id}: {$count} series");
            }

            sleep($delay);
        }

        $this->info('Discovery scan complete.');
        $this->line('Saved hits to: storage/app/' . $output);

        return self::SUCCESS;
    }

    private function query(): string
    {
        return <<<'GRAPHQL'
query ProbeTournament($titleId: ID!, $tournamentIds: [ID!]) {
  seriesStatistics(
    titleId: $titleId
    filter: {
      tournament: {
        id: { in: $tournamentIds }
        includeChildren: true
      }
    }
  ) {
    aggregationSeriesIds
    count
  }
}
GRAPHQL;
    }
}