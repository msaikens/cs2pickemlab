<?php

namespace App\Console\Commands;

use App\Models\SkinListing;
use App\Models\SteamAccount;
use App\Services\Steam\SteamInventoryService;
use App\Services\Steam\SteamProfileService;
use Illuminate\Console\Command;
use RuntimeException;

class SweepSteamMarketplaceAccounts extends Command
{
    protected $signature = 'marketplace:steam-sweep {--user-id=}';

    protected $description = 'Verify linked Steam accounts and disable marketplace listings when Steam privacy blocks verification.';

    public function handle(
        SteamProfileService $steamProfileService,
        SteamInventoryService $steamInventoryService
    ): int {
        $query = SteamAccount::query()
            ->with(['user.steamTradeProfile'])
            ->orderBy('id');

        if ($this->option('user-id')) {
            $query->where('user_id', $this->option('user-id'));
        }

        $checked = 0;
        $disabledListings = 0;

        $query->chunkById(100, function ($steamAccounts) use (
            $steamProfileService,
            $steamInventoryService,
            &$checked,
            &$disabledListings
        ): void {
            foreach ($steamAccounts as $steamAccount) {
                $checked++;

                $user = $steamAccount->user;

                if (! $user) {
                    continue;
                }

                try {
                    $steamProfile = $steamProfileService->getPlayerSummary($steamAccount->steam_id_64);

                    $steamAccount->update([
                        'persona_name' => $steamProfile['persona_name'],
                        'profile_url' => $steamProfile['profile_url'],
                        'avatar_url' => $steamProfile['avatar_url'],
                        'profile_visibility' => $steamProfile['profile_visibility'],
                        'verification_status' => 'verified',
                        'verification_failed_reason' => null,
                        'last_verified_at' => now(),
                        'last_marketplace_sweep_at' => now(),
                    ]);
                } catch (RuntimeException $exception) {
                    $steamAccount->update([
                        'verification_status' => 'failed',
                        'verification_failed_reason' => $exception->getMessage(),
                        'last_marketplace_sweep_at' => now(),
                    ]);

                    $disabledListings += $this->cancelOpenListings($user->id);

                    continue;
                }

                try {
                    $synced = $steamInventoryService->syncUserInventory($user);
                    $inventoryPublic = true;
                    } 
                catch (RuntimeException) {
                    $inventoryPublic = false;
                }

                if ($user->steamTradeProfile) {
                    $user->steamTradeProfile->update([
                        'inventory_public' => $inventoryPublic,
                        'last_inventory_sync_at' => now(),
                    ]);
                }

                $profilePublic = (string) $steamAccount->profile_visibility === '3';

                if (! $profilePublic || ! $inventoryPublic) {
                    $disabledListings += $this->cancelOpenListings($user->id);
                }
            }
        });

        $this->info("Checked {$checked} Steam account(s).");
        $this->info("Cancelled/disabled {$disabledListings} listing(s).");

        return self::SUCCESS;
    }

    private function cancelOpenListings(int $userId): int
    {
        return SkinListing::where('user_id', $userId)
            ->whereIn('status', ['draft', 'active', 'pending'])
            ->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);
    }
}