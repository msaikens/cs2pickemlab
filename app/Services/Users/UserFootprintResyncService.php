<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Steam\SteamInventoryService;
use App\Services\Steam\SteamProfileService;
use App\Services\Steam\SteamTradeUrlParser;
use Illuminate\Support\Facades\DB;

class UserFootprintResyncService
{
    public function __construct(
        private readonly SteamInventoryService $steamInventoryService,
        private readonly SteamProfileService $steamProfileService,
        private readonly SteamTradeUrlParser $steamTradeUrlParser,
    ) {
    }

    public function resync(User $user): array
    {
        $results = [
            'profile_created' => false,
            'steam_trade_profile_created' => false,
            'steam_profile_refreshed' => false,
            'trade_url_reparsed' => false,
            'inventory_synced' => false,
            'inventory_count' => 0,
            'warnings' => [],
        ];

        DB::transaction(function () use ($user, &$results): void {
            $user->load(['profile', 'steamAccount', 'steamTradeProfile']);

            if (! $user->profile) {
                $user->profile()->firstOrCreate([
                    'user_id' => $user->id,
                ]);

                $results['profile_created'] = true;
            }

            if ($user->steamAccount && ! $user->steamTradeProfile) {
                $user->steamTradeProfile()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['inventory_public' => false]
                );

                $results['steam_trade_profile_created'] = true;
            }
        });

        $user->refresh()->load(['profile', 'steamAccount', 'steamTradeProfile']);

        if (! $user->steamAccount) {
            $results['warnings'][] = 'Steam account is not linked.';
            return $results;
        }

        try {
            $summary = $this->steamProfileService->getPlayerSummary(
                $user->steamAccount->steam_id_64
            );

            $user->steamAccount->update([
                'steam_id_64' => $summary['steam_id_64'],
                'persona_name' => $summary['persona_name'],
                'profile_url' => $summary['profile_url'],
                'avatar_url' => $summary['avatar_url'],
                'profile_visibility' => $summary['profile_visibility'],
                'last_verified_at' => now(),
            ]);

            $results['steam_profile_refreshed'] = true;
        } catch (\Throwable $exception) {
            report($exception);
            $results['warnings'][] = 'Steam profile refresh failed: ' . $exception->getMessage();
        }

        $user->refresh()->load(['steamAccount', 'steamTradeProfile']);

        if (! $user->steamTradeProfile) {
            $user->steamTradeProfile()->firstOrCreate(
                ['user_id' => $user->id],
                ['inventory_public' => false]
            );

            $results['steam_trade_profile_created'] = true;
            $user->refresh()->load('steamTradeProfile');
        }

        if ($user->steamTradeProfile?->steam_trade_url) {
            try {
                $parsed = $this->steamTradeUrlParser->parse(
                    $user->steamTradeProfile->steam_trade_url
                );

                $user->steamTradeProfile->update([
                    'trade_partner_id' => $parsed['partner'],
                    'trade_token' => $parsed['token'],
                ]);

                $results['trade_url_reparsed'] = true;
            } catch (\Throwable $exception) {
                report($exception);
                $results['warnings'][] = 'Steam trade URL could not be re-parsed: ' . $exception->getMessage();
            }
        }

        try {
            $count = $this->steamInventoryService->syncUserInventory($user->fresh());

            $results['inventory_synced'] = true;
            $results['inventory_count'] = $count;
        } catch (\Throwable $exception) {
            report($exception);
            $results['warnings'][] = 'Steam inventory sync failed: ' . $exception->getMessage();
        }

        return $results;
    }
}