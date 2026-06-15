<?php

namespace App\Jobs\Grid;

use App\Models\GridImportRun;
use App\Models\GridTournamentCache;
use App\Support\AdminReporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class SearchGridTournamentsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const GRID_MAX_PAGE_SIZE = 50;
    private const PULL_ALL_MAX_PAGES = 50;
    private const SEARCH_MAX_PAGES = 20;

    public int $timeout = 300;

    public function __construct(
        public int $runId
    ) {
    }

    public function handle(): void
    {
        $run = GridImportRun::findOrFail($this->runId);
        $search = '';

        $run->update([
            'status' => 'running',
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $search = trim((string) data_get($run->input, 'search', ''));

            $endpoint = config('services.grid.central_data_endpoint');
            $apiKey = config('services.grid.api_key');
            $titleId = (string) config('services.grid.cs2_title_id', '28');

            if (! $endpoint) {
                throw new RuntimeException('GRID Central Data Feed endpoint is missing.');
            }

            if (! $apiKey) {
                throw new RuntimeException('GRID API key is missing.');
            }

            if ($titleId === '') {
                throw new RuntimeException('GRID CS2 title ID is missing.');
            }

            $count = $search === ''
                ? $this->pullTournamentPages($endpoint, $apiKey, $titleId)
                : $this->searchTournamentPages($endpoint, $apiKey, $search, $titleId);

            $run->update([
                'status' => 'completed',
                'output' => [
                    'search' => $search,
                    'mode' => $search === '' ? 'paginated_cs2' : 'cs2_name_search',
                    'grid_title_id' => $titleId,
                    'count' => $count,
                    'note' => 'CS2 tournament rows were upserted into grid_tournament_cache. Full results were not stored in this import run.',
                ],
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            AdminReporter::report($e, 'GRID tournament search failed', [
                'run_id' => $run->id ?? null,
                'search' => $search,
                'action' => $run->action ?? null,
            ]);

            $run->update([
                'status' => 'failed',
                'error_message' => $this->friendlyErrorMessage($e),
                'output' => [
                    'search' => $search,
                    'mode' => $search === '' ? 'paginated_cs2' : 'cs2_name_search',
                    'admin_error' => $e->getMessage(),
                    'note' => 'The GRID request failed safely. The admin page can continue to be used.',
                ],
                'finished_at' => now(),
            ]);

            return;
        }
    }

    private function pullTournamentPages(string $endpoint, string $apiKey, string $titleId): int
    {
        $after = null;
        $page = 1;
        $saved = 0;

        do {
            $json = $this->postGraphql($endpoint, $apiKey, $this->allQuery(), [
                'titleIds' => [$titleId],
                'first' => self::GRID_MAX_PAGE_SIZE,
                'after' => $after,
            ]);

            $connection = data_get($json, 'data.tournaments', []);
            $edges = data_get($connection, 'edges', []);

            $saved += $this->upsertTournamentEdges($edges, $titleId);

            $hasNextPage = (bool) data_get($connection, 'pageInfo.hasNextPage', false);
            $after = data_get($connection, 'pageInfo.endCursor');

            $page++;

            sleep(1);
        } while ($hasNextPage && $after && $page <= self::PULL_ALL_MAX_PAGES);

        return $saved;
    }

    private function searchTournamentPages(string $endpoint, string $apiKey, string $search, string $titleId): int
    {
        $after = null;
        $page = 1;
        $saved = 0;

        do {
            $json = $this->postGraphql($endpoint, $apiKey, $this->searchQuery(), [
                'titleIds' => [$titleId],
                'search' => $search,
                'first' => self::GRID_MAX_PAGE_SIZE,
                'after' => $after,
            ]);

            $connection = data_get($json, 'data.tournaments', []);
            $edges = data_get($connection, 'edges', []);

            $saved += $this->upsertTournamentEdges($edges, $titleId);

            $hasNextPage = (bool) data_get($connection, 'pageInfo.hasNextPage', false);
            $after = data_get($connection, 'pageInfo.endCursor');

            $page++;

            sleep(1);
        } while ($hasNextPage && $after && $page <= self::SEARCH_MAX_PAGES);

        return $saved;
    }

    private function upsertTournamentEdges(array $edges, string $titleId): int
    {
        $saved = 0;

        foreach ($edges as $edge) {
            $node = $edge['node'] ?? [];

            $id = $node['id'] ?? null;
            $name = $node['name'] ?? null;

            if (! $id || ! $name) {
                continue;
            }

            GridTournamentCache::updateOrCreate(
                [
                    'grid_tournament_id' => (string) $id,
                ],
                [
                    'name' => (string) $name,
                    'grid_title_id' => $titleId,
                    'is_cs2' => $titleId === '28',
                    'last_seen_at' => now(),
                ]
            );

            $saved++;
        }

        return $saved;
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

    private function friendlyErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'page size')) {
            return 'GRID rejected the tournament request because the page size was too large. The import was stopped safely.';
        }

        if (str_contains($message, 'API key')) {
            return 'GRID import could not run because the API key is missing or invalid.';
        }

        if (str_contains($message, 'endpoint')) {
            return 'GRID import could not run because the Central Data endpoint is missing.';
        }

        if (str_contains($message, 'timed out') || str_contains($message, 'cURL error 28')) {
            return 'GRID did not respond before the request timed out. Try again later.';
        }

        if (str_contains($message, '401') || str_contains($message, '403')) {
            return 'GRID rejected the request because the credentials or permissions were not accepted.';
        }

        return 'GRID import failed safely. An admin can review the saved error details.';
    }

    private function allQuery(): string
    {
        return <<<'GRAPHQL'
query PullTournaments($titleIds: [ID!]!, $first: Int!, $after: String) {
  tournaments(
    first: $first
    after: $after
    filter: {
      title: {
        id: {
          in: $titleIds
        }
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
        name
      }
    }
  }
}
GRAPHQL;
    }

    private function searchQuery(): string
    {
        return <<<'GRAPHQL'
query SearchTournaments($titleIds: [ID!]!, $search: String!, $first: Int!, $after: String) {
  tournaments(
    first: $first
    after: $after
    filter: {
      title: {
        id: {
          in: $titleIds
        }
      }
      name: {
        contains: $search
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
        name
      }
    }
  }
}
GRAPHQL;
    }
}