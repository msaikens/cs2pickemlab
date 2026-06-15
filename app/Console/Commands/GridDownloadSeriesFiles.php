<?php

namespace App\Console\Commands;

use App\Services\Grid\GridFileDownloadClient;
use Illuminate\Console\Command;

class GridDownloadSeriesFiles extends Command
{
    protected $signature = 'grid:download-series-files
        {series_id}
        {--event-zip : Download GRID events JSONL zip}
        {--end-state : Download GRID end-state JSON}';

    protected $description = 'Download GRID event/end-state files for a series.';

    public function handle(GridFileDownloadClient $client): int
    {
        $seriesId = (string) $this->argument('series_id');

        if (! $this->option('event-zip') && ! $this->option('end-state')) {
            $this->error('Choose --event-zip, --end-state, or both.');
            return self::FAILURE;
        }

        if ($this->option('event-zip')) {
            $path = $client->downloadGridEventsZip(
                $seriesId,
                "grid/series/{$seriesId}/events-grid.zip"
            );

            $this->info("Downloaded GRID events zip:");
            $this->line($path);
        }

        if ($this->option('end-state')) {
            $path = $client->downloadGridEndState(
                $seriesId,
                "grid/series/{$seriesId}/end-state-grid.json"
            );

            $this->info("Downloaded GRID end-state JSON:");
            $this->line($path);
        }

        return self::SUCCESS;
    }
}