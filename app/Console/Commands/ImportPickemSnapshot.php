<?php

namespace App\Console\Commands;

use App\Services\Pickem\PickemSnapshotImporter;
use Illuminate\Console\Command;

class ImportPickemSnapshot extends Command
{
    protected $signature = 'pickem:import-snapshot
        {path : Directory containing Pick’em CSV files}
        {--fresh-demo=0 : Delete demo PickLab event data before import}';

    protected $description = 'Import Pick’em event, stage, team, roster, stat, match, prediction, and recommendation CSV files.';

    public function handle(PickemSnapshotImporter $importer): int
    {
        $path = (string) $this->argument('path');

        if (! str_starts_with($path, '/')) {
            $path = base_path($path);
        }

        if ($this->option('fresh-demo')) {
            $this->warn('fresh-demo was requested, but destructive cleanup is intentionally not implemented here.');
            $this->warn('Delete demo rows manually only after confirming real event IDs.');
        }

        $this->info("Importing Pick’em snapshot from:");
        $this->line($path);

        $results = $importer->importDirectory($path);

        foreach ($results as $section => $count) {
            $this->line(str_pad($section, 20) . $count);
        }

        $this->info('Pick’em snapshot import complete.');

        return self::SUCCESS;
    }
}