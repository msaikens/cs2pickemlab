<?php

namespace App\Services\Grid;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GridClient
{
    public function statsFeedQuery(string $query, array $variables = []): array
    {
        return $this->query(
            config('services.grid.stats_feed_endpoint'),
            $query,
            $variables
        );
    }

    public function seriesStateQuery(string $query, array $variables = []): array
    {
        return $this->query(
            config('services.grid.series_state_endpoint'),
            $query,
            $variables
        );
    }

    private function query(?string $endpoint, string $query, array $variables = []): array
    {
        $apiKey = config('services.grid.api_key');

        if (! $endpoint) {
            throw new RuntimeException('GRID endpoint is missing.');
        }

        if (! $apiKey) {
            throw new RuntimeException('GRID API key is missing.');
        }

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
                'GRID request failed: '
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

        return $json['data'] ?? [];
    }
}