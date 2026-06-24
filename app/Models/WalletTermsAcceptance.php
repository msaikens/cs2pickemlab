<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTermsAcceptance extends Model
{
    public const SOURCE_WALLET_TERMS_PAGE = 'wallet_terms_page';
    public const SOURCE_TOP_UP_GATE = 'top_up_gate';
    public const SOURCE_MARKETPLACE_GATE = 'marketplace_gate';
    public const SOURCE_WITHDRAWAL_GATE = 'withdrawal_gate';

    protected $fillable = [
        'user_id',
        'terms_version',
        'accepted_at',
        'ip_address',
        'user_agent',
        'source',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function currentTermsVersion(): string
    {
        return (string) config('wallet.terms.version', 'v1');
    }

    public static function validSources(): array
    {
        return [
            self::SOURCE_WALLET_TERMS_PAGE,
            self::SOURCE_TOP_UP_GATE,
            self::SOURCE_MARKETPLACE_GATE,
            self::SOURCE_WITHDRAWAL_GATE,
        ];
    }

    public function scopeCurrentVersion(Builder $query): Builder
    {
        return $query->where('terms_version', self::currentTermsVersion());
    }
}