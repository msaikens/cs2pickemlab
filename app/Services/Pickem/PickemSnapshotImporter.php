<?php

namespace App\Services\Pickem;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class PickemSnapshotImporter
{
    private string $rosterTable;
    private string $teamStatsTable;

    public function __construct()
    {
        $this->rosterTable = Schema::hasTable('event_roster_players')
            ? 'event_roster_players'
            : 'event_rosters';

        $this->teamStatsTable = Schema::hasTable('team_stat_snapshots')
            ? 'team_stat_snapshots'
            : 'team_snapshots';
    }

    public function importDirectory(string $directory): array
    {
        if (! is_dir($directory)) {
            throw new RuntimeException("Import directory does not exist: {$directory}");
        }

        return DB::transaction(function () use ($directory) {
            $results = [];

            $results['events'] = $this->importEvents($directory);
            $results['stages'] = $this->importStages($directory);
            $results['teams'] = $this->importTeams($directory);
            $results['players'] = $this->importPlayers($directory);
            $results['rosters'] = $this->importRosters($directory);
            $results['team_stats'] = $this->importTeamStats($directory);
            $results['player_stats'] = $this->importPlayerStats($directory);
            $results['matches'] = $this->importMatches($directory);
            $results['predictions'] = $this->importPredictions($directory);
            $results['recommendations'] = $this->importRecommendations($directory);

            return $results;
        });
    }

    private function importEvents(string $directory): int
    {
        $rows = $this->csv($directory, 'events.csv');
        $count = 0;

        foreach ($rows as $row) {
            $name = $this->required($row, 'name');
            $slug = $this->slug($row['slug'] ?? $name);

            $this->upsert('events', ['slug' => $slug], [
                'name' => $name,
                'slug' => $slug,
                'organizer' => $row['organizer'] ?? null,
                'location' => $row['location'] ?? null,
                'starts_on' => $this->blankToNull($row['starts_on'] ?? null),
                'ends_on' => $this->blankToNull($row['ends_on'] ?? null),
                'status' => $row['status'] ?? 'live',
                'has_pickem' => $this->bool($row['has_pickem'] ?? true),
                'is_featured' => $this->bool($row['is_featured'] ?? false),
                'summary' => $row['summary'] ?? null,
                'notes' => $row['notes'] ?? null,
                'grid_id' => $row['grid_id'] ?? null,
                'hltv_id' => $row['hltv_id'] ?? null,
                'liquipedia_slug' => $row['liquipedia_slug'] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    private function importStages(string $directory): int
    {
        $rows = $this->csv($directory, 'stages.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->eventBySlug($this->required($row, 'event_slug'));

            $name = $this->required($row, 'name');
            $slug = $this->slug($row['slug'] ?? $name);

            $this->upsert('event_stages', [
                'event_id' => $event->id,
                'slug' => $slug,
            ], [
                'event_id' => $event->id,
                'name' => $name,
                'slug' => $slug,
                'starts_on' => $this->blankToNull($row['starts_on'] ?? null),
                'ends_on' => $this->blankToNull($row['ends_on'] ?? null),
                'format' => $row['format'] ?? 'swiss',
                'has_pickem' => $this->bool($row['has_pickem'] ?? true),
                'sort_order' => $this->int($row['sort_order'] ?? 0),
                'summary' => $row['summary'] ?? null,
                'notes' => $row['notes'] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    private function importTeams(string $directory): int
    {
        $rows = $this->csv($directory, 'teams.csv');
        $count = 0;

        foreach ($rows as $row) {
            $name = $this->required($row, 'name');
            $slug = $this->slug($row['slug'] ?? $name);

            $this->upsert('teams', ['slug' => $slug], [
                'name' => $name,
                'slug' => $slug,
                'region' => $row['region'] ?? null,
                'country' => $row['country'] ?? null,
                'logo_path' => $row['logo_path'] ?? null,
                'picklab_rating' => $this->decimalOrNull($row['picklab_rating'] ?? null),
                'status' => $row['status'] ?? 'active',
                'summary' => $row['summary'] ?? null,
                'notes' => $row['notes'] ?? null,
                'grid_id' => $row['grid_id'] ?? null,
                'hltv_id' => $row['hltv_id'] ?? null,
                'world_rank' => $this->intOrNull($row['world_rank'] ?? null),
                'ranking_points' => $this->decimalOrNull($row['ranking_points'] ?? null),
            ]);

            $count++;
        }

        return $count;
    }

    private function importPlayers(string $directory): int
    {
        $rows = $this->csv($directory, 'players.csv');
        $count = 0;

        foreach ($rows as $row) {
            $handle = $this->required($row, 'handle');
            $team = $this->optionalTeamBySlug($row['team_slug'] ?? null);

            $slug = $this->slug($row['slug'] ?? (($team?->slug ? $team->slug . '-' : '') . $handle));

            $this->upsert('players', ['slug' => $slug], [
                'team_id' => $team?->id,
                'handle' => $handle,
                'slug' => $slug,
                'real_name' => $row['real_name'] ?? null,
                'country' => $row['country'] ?? null,
                'age' => $this->intOrNull($row['age'] ?? null),
                'role' => $row['role'] ?? null,
                'photo_path' => $row['photo_path'] ?? null,
                'rating' => $this->decimalOrNull($row['rating'] ?? null),
                'kd_ratio' => $this->decimalOrNull($row['kd_ratio'] ?? null),
                'impact_rating' => $this->decimalOrNull($row['impact_rating'] ?? null),
                'status' => $row['status'] ?? 'active',
                'summary' => $row['summary'] ?? null,
                'notes' => $row['notes'] ?? null,
                'grid_id' => $row['grid_id'] ?? null,
                'hltv_id' => $row['hltv_id'] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    private function importRosters(string $directory): int
    {
        $rows = $this->csv($directory, 'rosters.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->eventBySlug($this->required($row, 'event_slug'));
            $stage = $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null);
            $team = $this->teamBySlug($this->required($row, 'team_slug'));
            $player = $this->playerBySlug($this->required($row, 'player_slug'));

            $this->upsert($this->rosterTable, [
                'event_id' => $event->id,
                'team_id' => $team->id,
                'player_id' => $player->id,
            ], [
                'event_id' => $event->id,
                'event_stage_id' => $stage?->id,
                'team_id' => $team->id,
                'player_id' => $player->id,
                'role' => $row['role'] ?? 'player',
                'is_starter' => $this->bool($row['is_starter'] ?? true),
                'is_active' => $this->bool($row['is_active'] ?? true),
                'locked_at' => $this->blankToNull($row['locked_at'] ?? null),
                'source_payload' => $this->jsonOrNull($row['source_payload'] ?? null),
            ]);

            $count++;
        }

        return $count;
    }

    private function importTeamStats(string $directory): int
    {
        $rows = $this->csv($directory, 'team_stats.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->optionalEventBySlug($row['event_slug'] ?? null);
            $stage = $event ? $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null) : null;
            $team = $this->teamBySlug($this->required($row, 'team_slug'));

            $snapshotDate = $this->required($row, 'snapshot_date');
            $scope = $row['scope'] ?? 'recent';

            $this->upsert($this->teamStatsTable, [
                'team_id' => $team->id,
                'event_id' => $event?->id,
                'event_stage_id' => $stage?->id,
                'source' => $row['source'] ?? 'manual_csv',
                'scope' => $scope,
                'snapshot_date' => $snapshotDate,
            ], [
                'team_id' => $team->id,
                'event_id' => $event?->id,
                'event_stage_id' => $stage?->id,
                'source' => $row['source'] ?? 'manual_csv',
                'scope' => $scope,
                'snapshot_date' => $snapshotDate,
                'matches_played' => $this->intOrNull($row['matches_played'] ?? null),
                'maps_played' => $this->intOrNull($row['maps_played'] ?? null),
                'match_win_rate' => $this->decimalOrNull($row['match_win_rate'] ?? null),
                'map_win_rate' => $this->decimalOrNull($row['map_win_rate'] ?? null),
                'round_win_rate' => $this->decimalOrNull($row['round_win_rate'] ?? null),
                'ct_round_win_rate' => $this->decimalOrNull($row['ct_round_win_rate'] ?? null),
                't_round_win_rate' => $this->decimalOrNull($row['t_round_win_rate'] ?? null),
                'pistol_win_rate' => $this->decimalOrNull($row['pistol_win_rate'] ?? null),
                'average_player_rating' => $this->decimalOrNull($row['average_player_rating'] ?? null),
                'average_adr' => $this->decimalOrNull($row['average_adr'] ?? null),
                'form_score' => $this->decimalOrNull($row['form_score'] ?? null),
                'map_pool' => $this->jsonOrNull($row['map_pool'] ?? null),
                'source_payload' => $this->jsonOrNull($row['source_payload'] ?? null),
            ]);

            $count++;
        }

        return $count;
    }

    private function importPlayerStats(string $directory): int
    {
        $rows = $this->csv($directory, 'player_stats.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->optionalEventBySlug($row['event_slug'] ?? null);
            $stage = $event ? $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null) : null;
            $team = $this->optionalTeamBySlug($row['team_slug'] ?? null);
            $player = $this->playerBySlug($this->required($row, 'player_slug'));

            $snapshotDate = $this->required($row, 'snapshot_date');
            $scope = $row['scope'] ?? 'recent';

            $this->upsert('player_stat_snapshots', [
                'player_id' => $player->id,
                'team_id' => $team?->id,
                'event_id' => $event?->id,
                'event_stage_id' => $stage?->id,
                'source' => $row['source'] ?? 'manual_csv',
                'scope' => $scope,
                'snapshot_date' => $snapshotDate,
            ], [
                'player_id' => $player->id,
                'team_id' => $team?->id,
                'event_id' => $event?->id,
                'event_stage_id' => $stage?->id,
                'source' => $row['source'] ?? 'manual_csv',
                'scope' => $scope,
                'snapshot_date' => $snapshotDate,
                'rating' => $this->decimalOrNull($row['rating'] ?? null),
                'kd_ratio' => $this->decimalOrNull($row['kd_ratio'] ?? null),
                'impact_rating' => $this->decimalOrNull($row['impact_rating'] ?? null),
                'adr' => $this->decimalOrNull($row['adr'] ?? null),
                'kast' => $this->decimalOrNull($row['kast'] ?? null),
                'kpr' => $this->decimalOrNull($row['kpr'] ?? null),
                'dpr' => $this->decimalOrNull($row['dpr'] ?? null),
                'headshot_percentage' => $this->decimalOrNull($row['headshot_percentage'] ?? null),
                'maps_played' => $this->intOrNull($row['maps_played'] ?? null),
                'rounds_played' => $this->intOrNull($row['rounds_played'] ?? null),
                'source_payload' => $this->jsonOrNull($row['source_payload'] ?? null),
            ]);

            $count++;
        }

        return $count;
    }

    private function importMatches(string $directory): int
    {
        $rows = $this->csv($directory, 'matches.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->eventBySlug($this->required($row, 'event_slug'));
            $stage = $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null);
            $teamOne = $this->teamBySlug($this->required($row, 'team_one_slug'));
            $teamTwo = $this->teamBySlug($this->required($row, 'team_two_slug'));
            $winner = $this->optionalTeamBySlug($row['winner_team_slug'] ?? null);

            $matchKey = [
                'event_id' => $event->id,
                'event_stage_id' => $stage?->id,
                'team_one_id' => $teamOne->id,
                'team_two_id' => $teamTwo->id,
                'round_label' => $row['round_label'] ?? null,
            ];

            $this->upsert('matches', $matchKey, [
                'event_id' => $event->id,
                'event_stage_id' => $stage?->id,
                'team_one_id' => $teamOne->id,
                'team_two_id' => $teamTwo->id,
                'winner_team_id' => $winner?->id,
                'starts_at' => $this->blankToNull($row['starts_at'] ?? null),
                'status' => $row['status'] ?? 'scheduled',
                'format' => $row['format'] ?? 'bo3',
                'bracket_group' => $row['bracket_group'] ?? null,
                'round_label' => $row['round_label'] ?? null,
                'bracket_position' => $this->int($row['bracket_position'] ?? 0),
                'team_one_score' => $this->intOrNull($row['team_one_score'] ?? null),
                'team_two_score' => $this->intOrNull($row['team_two_score'] ?? null),
                'summary' => $row['summary'] ?? null,
                'notes' => $row['notes'] ?? null,
                'grid_id' => $row['grid_id'] ?? null,
                'hltv_id' => $row['hltv_id'] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    private function importPredictions(string $directory): int
    {
        $rows = $this->csv($directory, 'predictions.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->eventBySlug($this->required($row, 'event_slug'));
            $stage = $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null);
            $teamOne = $this->teamBySlug($this->required($row, 'team_one_slug'));
            $teamTwo = $this->teamBySlug($this->required($row, 'team_two_slug'));
            $winner = $this->optionalTeamBySlug($row['predicted_winner_team_slug'] ?? null);

            $match = DB::table('matches')
                ->where('event_id', $event->id)
                ->where('event_stage_id', $stage?->id)
                ->where('team_one_id', $teamOne->id)
                ->where('team_two_id', $teamTwo->id)
                ->first();

            if (! $match) {
                throw new RuntimeException("No match found for prediction: {$teamOne->slug} vs {$teamTwo->slug}");
            }

            $this->upsert('predictions', ['match_id' => $match->id], [
                'match_id' => $match->id,
                'source' => $row['source'] ?? 'manual_csv',
                'model_name' => $row['model_name'] ?? null,
                'model_version' => $row['model_version'] ?? null,
                'predicted_winner_team_id' => $winner?->id,
                'confidence_score' => $this->int($row['confidence_score'] ?? 50),
                'team_one_win_probability' => $this->intOrNull($row['team_one_win_probability'] ?? null),
                'team_two_win_probability' => $this->intOrNull($row['team_two_win_probability'] ?? null),
                'upset_risk' => $row['upset_risk'] ?? 'medium',
                'prediction_label' => $row['prediction_label'] ?? 'toss_up',
                'best_pickem_use' => $row['best_pickem_use'] ?? null,
                'status' => $row['status'] ?? 'draft',
                'is_premium' => $this->bool($row['is_premium'] ?? false),
                'headline' => $row['headline'] ?? null,
                'summary' => $row['summary'] ?? null,
                'reasoning' => $row['reasoning'] ?? null,
                'factors' => $this->jsonOrNull($row['factors'] ?? null),
                'input_snapshot' => $this->jsonOrNull($row['input_snapshot'] ?? null),
                'published_at' => $this->blankToNull($row['published_at'] ?? null),
                'generated_at' => $this->blankToNull($row['generated_at'] ?? null),
                'stale_at' => $this->blankToNull($row['stale_at'] ?? null),
            ]);

            $count++;
        }

        return $count;
    }

    private function importRecommendations(string $directory): int
    {
        $rows = $this->csv($directory, 'recommendations.csv');
        $count = 0;

        foreach ($rows as $row) {
            $event = $this->eventBySlug($this->required($row, 'event_slug'));
            $stage = $this->optionalStageBySlug($event->id, $row['stage_slug'] ?? null);
            $team = $this->teamBySlug($this->required($row, 'team_slug'));

            $this->upsert('pickem_recommendations', [
                'event_id' => $event->id,
                'event_stage_id' => $stage?->id,
                'team_id' => $team->id,
                'slot_type' => $this->required($row, 'slot_type'),
            ], [
                'event_id' => $event->id,
                'event_stage_id' => $stage?->id,
                'team_id' => $team->id,
                'slot_type' => $this->required($row, 'slot_type'),
                'risk_level' => $row['risk_level'] ?? 'medium',
                'confidence_score' => $this->int($row['confidence_score'] ?? 50),
                'status' => $row['status'] ?? 'draft',
                'is_premium' => $this->bool($row['is_premium'] ?? false),
                'sort_order' => $this->int($row['sort_order'] ?? 0),
                'headline' => $row['headline'] ?? null,
                'summary' => $row['summary'] ?? null,
                'reasoning' => $row['reasoning'] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    private function upsert(string $table, array $keys, array $values): void
    {
        $now = now();

        $values = $this->filterColumns($table, $values);
        $keys = $this->filterColumns($table, $keys);

        $existing = DB::table($table)->where($keys)->first();

        if ($existing) {
            DB::table($table)
                ->where('id', $existing->id)
                ->update(array_merge($values, [
                    'updated_at' => $now,
                ]));

            return;
        }

        DB::table($table)->insert(array_merge($keys, $values, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));
    }

    private function filterColumns(string $table, array $data): array
    {
        return collect($data)
            ->filter(fn ($value) => $value !== '')
            ->filter(fn ($_value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }

    private function csv(string $directory, string $filename): array
    {
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (! file_exists($path)) {
            return [];
        }

        $handle = fopen($path, 'rb');

        if (! $handle) {
            throw new RuntimeException("Could not open CSV: {$path}");
        }

        $headers = fgetcsv($handle);

        if (! $headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map(fn ($header) => trim((string) $header), $headers);

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count(array_filter($data, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = [];

            foreach ($headers as $index => $header) {
                $row[$header] = isset($data[$index]) ? trim((string) $data[$index]) : null;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function eventBySlug(string $slug): object
    {
        $event = $this->optionalEventBySlug($slug);

        if (! $event) {
            throw new RuntimeException("Event not found: {$slug}");
        }

        return $event;
    }

    private function optionalEventBySlug(?string $slug): ?object
    {
        if (! $slug) {
            return null;
        }

        return DB::table('events')->where('slug', $this->slug($slug))->first();
    }

    private function optionalStageBySlug(int $eventId, ?string $slug): ?object
    {
        if (! $slug) {
            return null;
        }

        return DB::table('event_stages')
            ->where('event_id', $eventId)
            ->where('slug', $this->slug($slug))
            ->first();
    }

    private function teamBySlug(string $slug): object
    {
        $team = $this->optionalTeamBySlug($slug);

        if (! $team) {
            throw new RuntimeException("Team not found: {$slug}");
        }

        return $team;
    }

    private function optionalTeamBySlug(?string $slug): ?object
    {
        if (! $slug) {
            return null;
        }

        return DB::table('teams')->where('slug', $this->slug($slug))->first();
    }

    private function playerBySlug(string $slug): object
    {
        $player = DB::table('players')->where('slug', $this->slug($slug))->first();

        if (! $player) {
            throw new RuntimeException("Player not found: {$slug}");
        }

        return $player;
    }

    private function required(array $row, string $key): string
    {
        $value = trim((string) ($row[$key] ?? ''));

        if ($value === '') {
            throw new RuntimeException("Missing required CSV column value: {$key}");
        }

        return $value;
    }

    private function slug(string $value): string
    {
        return Str::slug($value);
    }

    private function bool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y'], true);
    }

    private function int($value): int
    {
        return (int) ($value ?: 0);
    }

    private function intOrNull($value): ?int
    {
        return $this->blankToNull($value) === null ? null : (int) $value;
    }

    private function decimalOrNull($value): ?float
    {
        return $this->blankToNull($value) === null ? null : (float) $value;
    }

    private function blankToNull($value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function jsonOrNull($value): mixed
    {
        $value = $this->blankToNull($value);

        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON value in CSV: ' . $value);
        }

        return json_encode($decoded);
    }
}