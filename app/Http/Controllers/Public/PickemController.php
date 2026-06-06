<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventStage;
use App\Models\Matches;
use App\Models\PickemRecommendation;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PickemController extends Controller
{
    public function index(): View
    {
        $event = Event::query()
            ->where('has_pickem', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->orderByDesc('is_featured')
            ->latest()
            ->first();

        $events = Event::query()
            ->where('has_pickem', true)
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
            ->orderByDesc('is_featured')
            ->latest()
            ->get();

        $recommendations = PickemRecommendation::query()
            ->with(['event', 'stage', 'team'])
            ->where('status', 'published')
            ->when($event, fn ($query) => $query->where('event_id', $event->id))
            ->orderBy('slot_type')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('slot_type');

        $recommendationBuckets = $this->bucketRecommendations($recommendations);

        return view('public.pickem.index', compact(
            'event',
            'events',
            'recommendations',
            'recommendationBuckets'
        ));
    }

    public function show(Event $event): View
    {
        $stages = EventStage::query()
            ->where('event_id', $event->id)
            ->orderBy('sort_order')
            ->get();

        $matches = Matches::query()
            ->with(['event', 'stage', 'teamOne', 'teamTwo', 'winner', 'prediction'])
            ->where('event_id', $event->id)
            ->orderBy('event_stage_id')
            ->orderByRaw("CASE
                WHEN bracket_group = 'playoffs' THEN 1
                WHEN bracket_group = 'swiss' THEN 0
                ELSE 0
            END")
            ->orderByRaw("CASE
                WHEN round_label = 'Swiss Round 1' THEN 1
                WHEN round_label = 'Swiss Round 2' THEN 2
                WHEN round_label = 'Swiss Round 3' THEN 3
                WHEN round_label = 'Swiss Round 4' THEN 4
                WHEN round_label = 'Swiss Round 5' THEN 5
                WHEN round_label = 'Quarterfinals' THEN 10
                WHEN round_label = 'Semifinals' THEN 11
                WHEN round_label = 'Grand Final' THEN 12
                ELSE 99
            END")
            ->orderBy('bracket_position')
            ->orderBy('starts_at')
            ->get();

        $recommendations = PickemRecommendation::query()
            ->with(['event', 'stage', 'team'])
            ->where('event_id', $event->id)
            ->where('status', 'published')
            ->orderBy('slot_type')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('slot_type');

        $recommendationBuckets = $this->bucketRecommendations($recommendations);

        $playoffBracket = $matches
            ->where('bracket_group', 'playoffs')
            ->groupBy('round_label');

        $swissStageBoards = $this->buildSwissStageBoards($stages, $matches);

        return view('public.pickem.show', compact(
            'event',
            'stages',
            'matches',
            'recommendations',
            'recommendationBuckets',
            'playoffBracket',
            'swissStageBoards'
        ));
    }

    private function bucketRecommendations(Collection $recommendations): array
    {
        return [
            'three_zero' => collect()
                ->merge($recommendations->get('safe_3_0', collect()))
                ->merge($recommendations->get('risky_3_0', collect())),

            'advance' => collect()
                ->merge($recommendations->get('safe_advancement', collect()))
                ->merge($recommendations->get('risky_advancement', collect())),

            'zero_three' => collect()
                ->merge($recommendations->get('likely_0_3', collect())),

            'watch_avoid' => collect()
                ->merge($recommendations->get('upset_watch', collect()))
                ->merge($recommendations->get('avoid', collect())),
        ];
    }

    private function buildSwissStageBoards(Collection $stages, Collection $matches): Collection
    {
        return $stages
            ->map(function (EventStage $stage) use ($matches): array {
                $stageMatches = $matches
                    ->filter(function ($match) use ($stage) {
                        return (int) $match->event_stage_id === (int) $stage->id
                            && $match->bracket_group !== 'playoffs';
                    })
                    ->sortBy([
                        fn ($a, $b) => $this->roundOrder($a->round_label) <=> $this->roundOrder($b->round_label),
                        fn ($a, $b) => ($a->bracket_position ?? 0) <=> ($b->bracket_position ?? 0),
                        fn ($a, $b) => ($a->starts_at?->timestamp ?? 0) <=> ($b->starts_at?->timestamp ?? 0),
                    ])
                    ->values();

                $standings = $this->buildSwissStandings($stageMatches);
                $buckets = $this->bucketSwissStandings($standings);

                $rounds = $stageMatches
                    ->groupBy(fn ($match) => $match->round_label ?: 'Unassigned Round')
                    ->sortKeysUsing(fn ($a, $b) => $this->roundOrder($a) <=> $this->roundOrder($b));

                return [
                    'stage' => $stage,
                    'matches' => $stageMatches,
                    'standings' => $standings,
                    'buckets' => $buckets,
                    'rounds' => $rounds,
                ];
            })
            ->filter(function (array $board): bool {
                $stageName = strtolower($board['stage']->name ?? '');

                if (str_contains($stageName, 'playoff')) {
                    return false;
                }

                return $board['matches']->isNotEmpty()
                    || (bool) ($board['stage']->has_pickem ?? false);
            })
            ->values();
    }

    private function buildSwissStandings(Collection $matches): array
    {
        $standings = [];

        foreach ($matches as $match) {
            if ($match->teamOne) {
                $this->ensureTeam($standings, $match->teamOne);
            }

            if ($match->teamTwo) {
                $this->ensureTeam($standings, $match->teamTwo);
            }

            if ($match->status !== 'completed' || ! $match->winner_team_id) {
                continue;
            }

            $teamOneId = (int) $match->team_one_id;
            $teamTwoId = (int) $match->team_two_id;
            $winnerId = (int) $match->winner_team_id;

            if (! isset($standings[$teamOneId], $standings[$teamTwoId])) {
                continue;
            }

            if ($winnerId === $teamOneId) {
                $standings[$teamOneId]['wins']++;
                $standings[$teamTwoId]['losses']++;
            }

            if ($winnerId === $teamTwoId) {
                $standings[$teamTwoId]['wins']++;
                $standings[$teamOneId]['losses']++;
            }
        }

        foreach ($standings as &$standing) {
            $standing['record'] = $standing['wins'] . '-' . $standing['losses'];

            if ($standing['wins'] >= 3) {
                $standing['status'] = 'advanced';
            } elseif ($standing['losses'] >= 3) {
                $standing['status'] = 'eliminated';
            } else {
                $standing['status'] = 'alive';
            }
        }

        unset($standing);

        uasort($standings, function (array $a, array $b): int {
            return [
                $this->statusSortValue($a['status']),
                -$a['wins'],
                $a['losses'],
                $a['name'],
            ] <=> [
                $this->statusSortValue($b['status']),
                -$b['wins'],
                $b['losses'],
                $b['name'],
            ];
        });

        return $standings;
    }

    private function bucketSwissStandings(array $standings): array
    {
        $buckets = [
            'advanced' => [
                '3-0' => [],
                '3-1' => [],
                '3-2' => [],
            ],
            'alive' => [
                '2-0' => [],
                '2-1' => [],
                '2-2' => [],
                '1-0' => [],
                '1-1' => [],
                '1-2' => [],
                '0-0' => [],
                '0-1' => [],
                '0-2' => [],
            ],
            'eliminated' => [
                '2-3' => [],
                '1-3' => [],
                '0-3' => [],
            ],
        ];

        foreach ($standings as $standing) {
            $record = $standing['record'];

            if ($standing['status'] === 'advanced' && isset($buckets['advanced'][$record])) {
                $buckets['advanced'][$record][] = $standing;
                continue;
            }

            if ($standing['status'] === 'eliminated' && isset($buckets['eliminated'][$record])) {
                $buckets['eliminated'][$record][] = $standing;
                continue;
            }

            if (isset($buckets['alive'][$record])) {
                $buckets['alive'][$record][] = $standing;
                continue;
            }

            $buckets['alive'][$record] = $buckets['alive'][$record] ?? [];
            $buckets['alive'][$record][] = $standing;
        }

        return $buckets;
    }

    private function ensureTeam(array &$standings, $team): void
    {
        if (isset($standings[$team->id])) {
            return;
        }

        $standings[$team->id] = [
            'id' => $team->id,
            'name' => $team->name,
            'slug' => $team->slug ?? null,
            'wins' => 0,
            'losses' => 0,
            'record' => '0-0',
            'status' => 'alive',
        ];
    }

    private function roundOrder(?string $roundLabel): int
    {
        return match ($roundLabel) {
            'Swiss Round 1' => 1,
            'Swiss Round 2' => 2,
            'Swiss Round 3' => 3,
            'Swiss Round 4' => 4,
            'Swiss Round 5' => 5,
            'Quarterfinals' => 10,
            'Semifinals' => 11,
            'Grand Final' => 12,
            default => 99,
        };
    }

    private function statusSortValue(string $status): int
    {
        return match ($status) {
            'advanced' => 0,
            'alive' => 1,
            'eliminated' => 2,
            default => 3,
        };
    }
}
