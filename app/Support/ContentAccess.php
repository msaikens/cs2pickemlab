<?php

namespace App\Support;

use App\Models\ContentGate;
use App\Models\User;

class ContentAccess
{
    public function gate(string $gateKey): ?ContentGate
    {
        return ContentGate::query()
            ->where('gate_key', $gateKey)
            ->first();
    }

    public function allows(string $gateKey, ?User $user = null): bool
    {
        $gate = $this->gate($gateKey);

        if (! $gate) {
            return true;
        }

        if (! $gate->is_enabled) {
            return true;
        }

        if ($gate->requires_login && ! $user) {
            return false;
        }

        if ($gate->requires_subscription && ! $user?->hasActiveSubscription()) {
            return false;
        }

        return true;
    }

    public function lockedMessage(string $gateKey): string
    {
        $gate = $this->gate($gateKey);

        return $gate?->locked_message ?: 'This content is restricted.';
    }
}
