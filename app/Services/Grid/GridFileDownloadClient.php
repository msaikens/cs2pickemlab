<?php

namespace App\Services\Grid;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GridFileDownloadClient
{
    private function baseUrl(): string
    {
        return rtrim((string) config('services.grid.file_download_endpoint'), '/');
    }

    private function apiKey(): string
    {
        $key = (string) config('services.grid.api_key');

        if ($key === '') {
            throw new RuntimeException('GRID_API_KEY is missing.');
        }

        return $key;
    }

    public function listFiles(string $seriesId): array
    {
        $response = Http::withHeaders([
                'x-api-key' => $this->apiKey(),
            ])
            ->acceptJson()
            ->timeout(45)
            ->get($this->baseUrl() . "/list/{$seriesId}");

        if (! $response->successful()) {
            throw new RuntimeException(
                "GRID file list failed: {$response->status()} {$response->body()}"
            );
        }

        return $response->json('files') ?? [];
    }

    public function downloadGridEventsZip(string $seriesId, string $storagePath): string
    {
        return $this->download(
            "/events/grid/series/{$seriesId}",
            $storagePath
        );
    }

    public function downloadGridEndState(string $seriesId, string $storagePath): string
    {
        return $this->download(
            "/end-state/grid/series/{$seriesId}",
            $storagePath
        );
    }

    private function download(string $path, string $storagePath): string
    {
        $response = Http::withHeaders([
                'x-api-key' => $this->apiKey(),
            ])
            ->timeout(120)
            ->get($this->baseUrl() . $path);

        if (! $response->successful()) {
            throw new RuntimeException(
                "GRID file download failed: {$response->status()} {$response->body()}"
            );
        }

        Storage::put($storagePath, $response->body());

        return storage_path('app/' . $storagePath);
    }
}