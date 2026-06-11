<?php

namespace App\Services\Steam;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SteamProfileService
{
    public function getPlayerSummary(string $steamId64): array
    {
        $apiKey = config('services.steam.api_key');

        if (! $apiKey) {
            throw new RuntimeException('Steam Web API key is not configured.');
        }

        $response = Http::timeout(10)
            ->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
                'key' => $apiKey,
                'steamids' => $steamId64,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Unable to fetch Steam profile.');
        }

        $players = $response->json('response.players', []);

        if (empty($players)) {
            throw new RuntimeException('Steam profile was not found.');
        }

        $player = $players[0];

        return [
            'steam_id_64' => (string) ($player['steamid'] ?? $steamId64),
            'persona_name' => $player['personaname'] ?? null,
            'profile_url' => $player['profileurl'] ?? 'https://steamcommunity.com/profiles/' . $steamId64,
            'avatar_url' => $player['avatarfull'] ?? $player['avatarmedium'] ?? $player['avatar'] ?? null,
            'profile_visibility' => isset($player['communityvisibilitystate'])
                ? (string) $player['communityvisibilitystate']
                : null,
        ];
    }
}