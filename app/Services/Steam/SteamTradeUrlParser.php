<?php

namespace App\Services\Steam;

use InvalidArgumentException;

class SteamTradeUrlParser
{
    public function parse(string $tradeUrl): array
    {
        $tradeUrl = trim($tradeUrl);

        if ($tradeUrl === '') {
            throw new InvalidArgumentException('Steam trade URL is empty.');
        }

        $parts = parse_url($tradeUrl);

        if (! isset($parts['query'])) {
            throw new InvalidArgumentException('Steam trade URL is missing query parameters.');
        }

        parse_str($parts['query'], $query);

        $partner = $query['partner'] ?? null;
        $token = $query['token'] ?? null;

        if (! $partner || ! $token) {
            throw new InvalidArgumentException('Steam trade URL must include partner and token values.');
        }

        return [
            'partner' => (string) $partner,
            'token' => (string) $token,
        ];
    }
}