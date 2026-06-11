<?php

namespace App\Services\Steam;

use App\Models\SteamInventoryItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SteamInventoryService
{
    private const APP_ID = '730';
    private const CONTEXT_ID = '2';

    public function isCs2InventoryPublic(string $steamId64): bool
    {
        try {
            $json = $this->fetchInventoryPage($steamId64, 1);
        } catch (RuntimeException) {
            return false;
        }

        return is_array($json);
    }

    public function syncUserInventory(User $user): int
    {
        $user->load(['steamAccount', 'steamTradeProfile']);

        if (! $user->steamAccount) {
            throw new RuntimeException('No Steam account is linked.');
        }

        $steamId64 = $user->steamAccount->steam_id_64;

        $allAssets = [];
        $allDescriptions = [];
        $startAssetId = null;

        do {
            $json = $this->fetchInventoryPage($steamId64, 2000, $startAssetId);

            $assets = $json['assets'] ?? [];
            $descriptions = $json['descriptions'] ?? [];

            foreach ($assets as $asset) {
                if (isset($asset['assetid'])) {
                    $allAssets[] = $asset;
                }
            }

            foreach ($descriptions as $description) {
                $key = $this->descriptionKey(
                    (string) ($description['classid'] ?? ''),
                    (string) ($description['instanceid'] ?? '')
                );

                $allDescriptions[$key] = $description;
            }

            $more = (bool) ($json['more_items'] ?? false);
            $startAssetId = $json['last_assetid'] ?? null;
        } while ($more && $startAssetId);

        if ($user->steamTradeProfile) {
            $user->steamTradeProfile->update([
                'inventory_public' => true,
                'last_inventory_sync_at' => now(),
            ]);
        }

        $seenAssetIds = [];
        $synced = 0;

        foreach ($allAssets as $asset) {
            $assetId = (string) ($asset['assetid'] ?? '');

            if ($assetId === '') {
                continue;
            }

            $classId = (string) ($asset['classid'] ?? '');
            $instanceId = (string) ($asset['instanceid'] ?? '');

            $description = $allDescriptions[$this->descriptionKey($classId, $instanceId)] ?? [];

            $marketHashName = $description['market_hash_name']
                ?? $description['market_name']
                ?? $description['name']
                ?? null;

            if (! $marketHashName) {
                continue;
            }

            $tags = $description['tags'] ?? [];

            SteamInventoryItem::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'asset_id' => $assetId,
                ],
                [
                    'steam_id_64' => $steamId64,
                    'class_id' => $classId ?: null,
                    'instance_id' => $instanceId ?: null,
                    'app_id' => (string) ($asset['appid'] ?? self::APP_ID),
                    'context_id' => (string) ($asset['contextid'] ?? self::CONTEXT_ID),
                    'market_hash_name' => $marketHashName,
                    'name' => $description['name'] ?? null,
                    'type' => $description['type'] ?? null,
                    'rarity' => $this->tagValue($tags, 'Rarity'),
                    'exterior' => $this->tagValue($tags, 'Exterior'),
                    'icon_url' => $description['icon_url'] ?? null,
                    'image_url' => isset($description['icon_url'])
                        ? 'https://community.cloudflare.steamstatic.com/economy/image/' . $description['icon_url']
                        : null,
                    'tradable' => (int) ($description['tradable'] ?? 0) === 1,
                    'marketable' => (int) ($description['marketable'] ?? 0) === 1,
                    'commodity' => (int) ($description['commodity'] ?? 0) === 1,
                    'raw_asset' => $asset,
                    'raw_description' => $description,
                    'last_seen_at' => now(),
                ]
            );

            $seenAssetIds[] = $assetId;
            $synced++;
        }

        SteamInventoryItem::where('user_id', $user->id)
            ->whereNotIn('asset_id', $seenAssetIds)
            ->delete();

        return $synced;
    }

    private function fetchInventoryPage(
        string $steamId64,
        int $count = 2000,
        ?string $startAssetId = null
    ): array {
        $query = [
            'l' => 'english',
            'count' => $count,
        ];

        if ($startAssetId) {
            $query['start_assetid'] = $startAssetId;
        }

        $url = $this->inventoryUrl($steamId64);

        $response = Http::timeout(30)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'CS2PickLab/1.0 (+https://dev.cs2picklabs.com)',
            ])
            ->get($url, $query);

        if ($response->status() === 403) {
            throw new RuntimeException('Steam inventory is private or not publicly accessible.');
        }

        if ($response->status() === 429) {
            throw new RuntimeException('Steam is rate limiting inventory requests. Wait a few minutes and try again.');
        }

        if (! $response->successful()) {
            Log::warning('Steam inventory request failed.', [
                'steam_id_64' => $steamId64,
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 1000),
            ]);

            throw new RuntimeException('Steam inventory request failed with HTTP status ' . $response->status() . '.');
        }

        $json = $response->json();

        if (! is_array($json)) {
            Log::warning('Steam inventory response was not JSON.', [
                'steam_id_64' => $steamId64,
                'body' => mb_substr($response->body(), 0, 1000),
            ]);

            throw new RuntimeException('Steam returned an unreadable inventory response.');
        }

        /*
         * Empty inventories can be valid. Do not treat missing assets as an automatic failure
         * if Steam returned a valid JSON object.
         */
        if (! array_key_exists('assets', $json)) {
            $json['assets'] = [];
        }

        if (! array_key_exists('descriptions', $json)) {
            $json['descriptions'] = [];
        }

        return $json;
    }

    private function inventoryUrl(string $steamId64): string
    {
        return 'https://steamcommunity.com/inventory/'
            . $steamId64 . '/'
            . self::APP_ID . '/'
            . self::CONTEXT_ID;
    }

    private function descriptionKey(string $classId, string $instanceId): string
    {
        return $classId . ':' . $instanceId;
    }

    private function tagValue(array $tags, string $category): ?string
    {
        foreach ($tags as $tag) {
            if (($tag['category'] ?? null) === $category) {
                return $tag['localized_tag_name'] ?? $tag['name'] ?? null;
            }
        }

        return null;
    }
}