<?php

namespace App\Services\Marketplace;

class FeeCalculator
{
    public function calculateSellerFeeCents(int $salePriceCents): int
    {
        $feeBps = config('marketplace.seller_fee_bps', 500);
        $minimumFeeCents = config('marketplace.minimum_fee_cents', 50);

        $percentageFee = (int) ceil(($salePriceCents * $feeBps) / 10000);

        return max($percentageFee, $minimumFeeCents);
    }

    public function calculateSellerNetCents(int $salePriceCents): int
    {
        return max(0, $salePriceCents - $this->calculateSellerFeeCents($salePriceCents));
    }
}