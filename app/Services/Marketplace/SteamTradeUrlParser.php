<?php

namespace App\Services\Marketplace;

use InvalidArgumentException;

class SteamTradeUrlParser
{
    public function parse(string $tradeUrl): array
    {
        $parts = parse_url($tradeUrl);

        if (! isset($parts['host'], $parts['query'])) {
            throw new InvalidArgumentException('Invalid Steam trade URL.');
        }

        $host = strtolower($parts['host']);

        if (! str_contains($host, 'steamcommunity.com')) {
            throw new InvalidArgumentException('Trade URL must be a Steam Community URL.');
        }

        parse_str($parts['query'], $query);

        $partner = $query['partner'] ?? null;
        $token = $query['token'] ?? null;

        if (! $partner || ! $token) {
            throw new InvalidArgumentException('Trade URL must include partner and token values.');
        }

        return [
            'trade_partner_id' => (string) $partner,
            'trade_token' => (string) $token,
        ];
    }
}