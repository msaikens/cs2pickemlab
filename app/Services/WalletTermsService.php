<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTermsAcceptance;
use Illuminate\Http\Request;

class WalletTermsService
{
    public function currentVersion(): string
    {
        return WalletTermsAcceptance::currentTermsVersion();
    }

    public function hasAcceptedCurrentTerms(User $user): bool
    {
        return $user->walletTermsAcceptances()
            ->currentVersion()
            ->exists();
    }

    public function currentAcceptanceFor(User $user): ?WalletTermsAcceptance
    {
        return $user->walletTermsAcceptances()
            ->currentVersion()
            ->latest('accepted_at')
            ->first();
    }

    public function acceptCurrentTerms(
        User $user,
        Request $request,
        ?string $source = null
    ): WalletTermsAcceptance {
        return $user->walletTermsAcceptances()->firstOrCreate(
            [
                'terms_version' => $this->currentVersion(),
            ],
            [
                'accepted_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'source' => $this->cleanSource($source),
            ]
        );
    }

    public function cleanSource(?string $source): string
    {
        if (
            is_string($source)
            && in_array($source, WalletTermsAcceptance::validSources(), true)
        ) {
            return $source;
        }

        return WalletTermsAcceptance::SOURCE_WALLET_TERMS_PAGE;
    }
}