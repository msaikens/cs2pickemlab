<?php

namespace App\Console\Commands;

use App\Services\Grid\GridClient;
use Illuminate\Console\Command;

class GridProbeStats extends Command
{
    protected $signature = 'grid:probe-stats
        {--team_id= : GRID team ID}
        {--player_id= : GRID player ID}
        {--tournament_id= : GRID tournament ID}';

    protected $description = 'Probe GRID Stats Feed API for CS2 team/player statistics.';

    public function handle(GridClient $grid): int
    {
        $teamId = $this->option('team_id');
        $playerId = $this->option('player_id');
        $tournamentId = $this->option('tournament_id');

        if (! $teamId && ! $playerId) {
            $this->error('Provide --team_id or --player_id.');
            return self::FAILURE;
        }

        if (! $tournamentId) {
            $this->error('Provide --tournament_id.');
            return self::FAILURE;
        }

        if ($teamId) {
            $this->info('Testing GRID teamStatistics...');

            $data = $grid->statsFeedQuery($this->teamStatsQuery(), [
                'teamId' => $teamId,
                'tournamentIds' => [$tournamentId],
            ]);

            $this->line(json_encode($data, JSON_PRETTY_PRINT));
        }

        if ($playerId) {
            $this->info('Testing GRID playerStatistics...');

            $data = $grid->statsFeedQuery($this->playerStatsQuery(), [
                'playerId' => $playerId,
                'tournamentIds' => [$tournamentId],
            ]);

            $this->line(json_encode($data, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }

    private function teamStatsQuery(): string
    {
        return <<<'GRAPHQL'
query TeamStats($teamId: ID!, $tournamentIds: [ID!]) {
  teamStatistics(
    teamId: $teamId
    filter: {
      tournament: {
        id: { in: $tournamentIds }
        includeChildren: true
      }
    }
  ) {
    id
    aggregationSeriesIds
    series {
      count
      ... on Cs2TeamSeriesStatistics {
        kills { sum avg }
        deaths { sum avg }
        headshots { sum avg }
        score { sum avg }
        firstKill { value count percentage }
        won { value count percentage }
        duration { avg }
      }
    }
  }
}
GRAPHQL;
    }
// Query for player statistics.
    private function playerStatsQuery(): string
    {
        return <<<'GRAPHQL'
query PlayerStats($playerId: ID!, $tournamentIds: [ID!]) {
  playerStatistics(
    playerId: $playerId
    filter: {
      tournament: {
        id: { in: $tournamentIds }
        includeChildren: true
      }
    }
  ) {
    id
    aggregationSeriesIds
    series {
      count
      ... on Cs2PlayerSeriesStatistics {
        kills { sum avg }
        deaths { sum avg }
        headshots { sum avg }
        firstKill { value count percentage }
        won { value count percentage }
      }
    }
  }
}
GRAPHQL;
    }
}