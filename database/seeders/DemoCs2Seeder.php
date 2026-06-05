<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventStage;
use App\Models\GameMap;
use App\Models\Matches;
use App\Models\PickemRecommendation;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DemoCs2Seeder extends Seeder
{
    public function run(): void
    {
        $maps = [
            'Ancient',
            'Anubis',
            'Dust II',
            'Inferno',
            'Mirage',
            'Nuke',
            'Train',
        ];

        foreach ($maps as $map) {
            GameMap::create([
                'name' => $map,
                'slug' => str($map)->slug(),
                'status' => 'active',
            ]);
        }

        $mouz = Team::create([
            'name' => 'MOUZ',
            'slug' => 'mouz',
            'short_name' => 'MOUZ',
            'region' => 'Europe',
            'country' => 'International',
            'picklab_rating' => 1785,
            'status' => 'active',
            'summary' => 'A high-floor international roster with strong tournament consistency.',
        ]);

        $b8 = Team::create([
            'name' => 'B8',
            'slug' => 'b8',
            'short_name' => 'B8',
            'region' => 'Europe',
            'country' => 'Ukraine',
            'picklab_rating' => 1640,
            'status' => 'active',
            'summary' => 'Dangerous upset team with volatile form and map-specific threat.',
        ]);

        $heroic = Team::create([
            'name' => 'Heroic',
            'slug' => 'heroic',
            'short_name' => 'Heroic',
            'region' => 'Europe',
            'country' => 'International',
            'picklab_rating' => 1665,
            'status' => 'active',
            'summary' => 'A competitive roster with enough firepower to punish weaker preparation.',
        ]);

        $m80 = Team::create([
            'name' => 'M80',
            'slug' => 'm80',
            'short_name' => 'M80',
            'region' => 'North America',
            'country' => 'United States',
            'picklab_rating' => 1590,
            'status' => 'active',
            'summary' => 'North American contender with upset potential but consistency concerns.',
        ]);

        $flyquest = Team::create([
            'name' => 'FlyQuest',
            'slug' => 'flyquest',
            'short_name' => 'FQ',
            'region' => 'Oceania',
            'country' => 'Australia',
            'picklab_rating' => 1570,
            'status' => 'active',
            'summary' => 'Regional threat with experience but a narrower margin against elite teams.',
        ]);

        $sharks = Team::create([
            'name' => 'Sharks',
            'slug' => 'sharks',
            'short_name' => 'SHK',
            'region' => 'South America',
            'country' => 'Brazil',
            'picklab_rating' => 1495,
            'status' => 'active',
            'summary' => 'Underdog profile with a difficult path in stronger international fields.',
        ]);

        $this->createDemoPlayers($mouz, ['xertioN', 'torzsi', 'siuhy', 'Jimpphat', 'Brollan']);
        $this->createDemoPlayers($b8, ['npl', 'esenthial', 'headtr1ck', 'alex666', 'kensizor']);
        $this->createDemoPlayers($heroic, ['PlayerA', 'PlayerB', 'PlayerC', 'PlayerD', 'PlayerE']);
        $this->createDemoPlayers($m80, ['slaxz-', 'Swisher', 'reck', 's1n', 'Lake']);
        $this->createDemoPlayers($flyquest, ['dexter', 'INS', 'Liazz', 'aliStair', 'Vexite']);
        $this->createDemoPlayers($sharks, ['doc', 'rdnzao', 'gafolo', 'pepe', 'koala']);

        $event = Event::create([
            'name' => 'PickLab Demo Major',
            'slug' => 'picklab-demo-major',
            'organizer' => 'PickLab',
            'location' => 'Online Demo',
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addDays(7)->toDateString(),
            'status' => 'live',
            'has_pickem' => true,
            'is_featured' => true,
            'summary' => 'Demo event used to test PickLab predictions, Pick’em recommendations, and match pages.',
        ]);

        $stage = EventStage::create([
            'event_id' => $event->id,
            'name' => 'Stage 2',
            'slug' => 'stage-2',
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addDays(3)->toDateString(),
            'format' => 'swiss',
            'has_pickem' => true,
            'sort_order' => 1,
            'summary' => 'Demo Swiss stage for Pick’em testing.',
        ]);

        $matchOne = Matches::create([
            'event_id' => $event->id,
            'event_stage_id' => $stage->id,
            'team_one_id' => $mouz->id,
            'team_two_id' => $b8->id,
            'starts_at' => now()->addHours(5),
            'status' => 'scheduled',
            'format' => 'bo3',
            'summary' => 'MOUZ enter as the safer side, but B8 have enough volatility to make this uncomfortable.',
        ]);

        Prediction::create([
            'match_id' => $matchOne->id,
            'predicted_winner_team_id' => $mouz->id,
            'confidence_score' => 72,
            'upset_risk' => 'medium',
            'best_pickem_use' => 'safe_advancement',
            'status' => 'published',
            'is_premium' => false,
            'headline' => 'MOUZ are safer, but not a free 3:0',
            'summary' => 'MOUZ have the higher floor, but B8’s upset profile makes this better as an advancement pick than a perfect-run pick.',
            'reasoning' => 'MOUZ rate higher overall and have better consistency, while B8 carry map-specific threat and volatility. The recommendation is to use MOUZ safely but avoid overcommitting them as a 3:0 unless the surrounding bracket is weak.',
            'published_at' => now(),
        ]);

        $matchTwo = Matches::create([
            'event_id' => $event->id,
            'event_stage_id' => $stage->id,
            'team_one_id' => $heroic->id,
            'team_two_id' => $m80->id,
            'starts_at' => now()->addHours(8),
            'status' => 'scheduled',
            'format' => 'bo3',
            'summary' => 'Heroic have the stronger baseline, but M80 can make this messy.',
        ]);

        Prediction::create([
            'match_id' => $matchTwo->id,
            'predicted_winner_team_id' => $heroic->id,
            'confidence_score' => 64,
            'upset_risk' => 'high',
            'best_pickem_use' => 'risky_advancement',
            'status' => 'published',
            'is_premium' => false,
            'headline' => 'Heroic lean, but upset risk is real',
            'summary' => 'Heroic are the pick, but this is not a comfortable lock.',
            'reasoning' => 'Heroic have a better rating profile, but M80’s upset path is live if the veto breaks their way and the first map starts hot.',
            'published_at' => now(),
        ]);

        PickemRecommendation::create([
            'event_id' => $event->id,
            'event_stage_id' => $stage->id,
            'team_id' => $mouz->id,
            'slot_type' => 'safe_advancement',
            'risk_level' => 'low',
            'confidence_score' => 78,
            'status' => 'published',
            'is_premium' => false,
            'sort_order' => 1,
            'headline' => 'Safe advancement pick',
            'summary' => 'MOUZ are better used as a reliable advancement pick than a risky 3:0.',
            'reasoning' => 'They have strong consistency and a high floor, but their path still includes enough upset risk to avoid spending the 3:0 slot unless needed.',
        ]);

        PickemRecommendation::create([
            'event_id' => $event->id,
            'event_stage_id' => $stage->id,
            'team_id' => $b8->id,
            'slot_type' => 'upset_watch',
            'risk_level' => 'high',
            'confidence_score' => 58,
            'status' => 'published',
            'is_premium' => false,
            'sort_order' => 2,
            'headline' => 'Upset watch team',
            'summary' => 'B8 are not safe, but they are exactly the kind of team that can wreck a conservative Pick’em card.',
            'reasoning' => 'Their volatility makes them hard to trust, but also dangerous to ignore.',
        ]);

        PickemRecommendation::create([
            'event_id' => $event->id,
            'event_stage_id' => $stage->id,
            'team_id' => $sharks->id,
            'slot_type' => 'likely_0_3',
            'risk_level' => 'medium',
            'confidence_score' => 66,
            'status' => 'published',
            'is_premium' => false,
            'sort_order' => 3,
            'headline' => 'Possible 0:3 candidate',
            'summary' => 'Sharks have a difficult path and are a reasonable 0:3 consideration.',
            'reasoning' => 'The rating gap and regional strength concerns make them a candidate, but 0:3 picks are inherently dangerous.',
        ]);
    }

    private function createDemoPlayers(Team $team, array $handles): void
    {
        foreach ($handles as $index => $handle) {
            Player::create([
                'team_id' => $team->id,
                'handle' => $handle,
                'slug' => str($team->slug . '-' . $handle)->slug(),
                'country' => 'International',
                'role' => $index === 0 ? 'rifler' : null,
                'rating' => 1.00 + ($index * 0.03),
                'kd_ratio' => 1.00 + ($index * 0.02),
                'impact_rating' => 1.00 + ($index * 0.01),
                'status' => 'active',
                'summary' => 'Demo player record for layout and data testing.',
            ]);
        }
    }
}
