<?php

namespace App\Console\Commands;

use App\Services\Grid\GridFileDownloadClient;
use Illuminate\Console\Command;

class GridListSeriesFiles extends Command
{
    protected $signature = 'grid:list-series-files {series_id}';

    protected $description = 'List available GRID file downloads for a series.';

    public function handle(GridFileDownloadClient $client): int
    {
        $seriesId = (string) $this->argument('series_id');

        $files = $client->listFiles($seriesId);

        $rows = collect($files)->map(fn ($file) => [
            $file['id'] ?? '',
            $file['status'] ?? '',
            $file['fileName'] ?? '',
            $file['description'] ?? '',
            $file['fullURL'] ?? '',
        ])->all();

        $this->table(
            ['ID', 'Status', 'File Name', 'Description', 'Full URL'],
            $rows
        );

        return self::SUCCESS;
    }
}