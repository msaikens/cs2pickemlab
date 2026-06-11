<?php

namespace App\Models;

use App\Models\SkinListing;
use App\Models\SteamAccount;
use App\Models\SteamTradeProfile;
use App\Models\TradeRequest;
use App\Models\SteamInventoryItem;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory;
    use Notifiable;
    use CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'avatar_url',
        'role',
        'subscription_status',
        'status',
        'marketplace_terms_accepted_at',
        'subscription_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'marketplace_terms_accepted_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscription_status === 'active') {
            return true;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isFuture()) {
            return true;
        }

        return false;
    }
    
    public function steamInventoryItems(): HasMany
    {
        return $this->hasMany(SteamInventoryItem::class);
    }
    
    public function displayName(): string
    {
        return $this->profile?->display_name
            ?: $this->name
            ?: $this->email;
    }

    public function steamAccount(): HasOne
{
    return $this->hasOne(SteamAccount::class);
}

public function steamTradeProfile(): HasOne
{
    return $this->hasOne(SteamTradeProfile::class);
}

public function skinListings(): HasMany
{
    return $this->hasMany(SkinListing::class);
}

public function sentTradeRequests(): HasMany
{
    return $this->hasMany(TradeRequest::class, 'buyer_user_id');
}

public function receivedTradeRequests(): HasMany
{
    return $this->hasMany(TradeRequest::class, 'seller_user_id');
}

public function isActive(): bool
{
    return $this->status === 'active';
}

public function hasAcceptedMarketplaceTerms(): bool
{
    return $this->marketplace_terms_accepted_at !== null;
}

public function hasVerifiedSteamAccount(): bool
{
    return $this->steamAccount !== null
        && $this->steamAccount->steam_id_64 !== null
        && $this->steamAccount->last_verified_at !== null;
}

public function hasTradeProfileReady(): bool
{
    return $this->steamTradeProfile !== null
        && filled($this->steamTradeProfile->steam_trade_url)
        && filled($this->steamTradeProfile->trade_partner_id)
        && filled($this->steamTradeProfile->trade_token);
}

public function hasPublicSteamProfile(): bool
{
    return $this->steamAccount !== null
        && (string) $this->steamAccount->profile_visibility === '3';
}

public function hasPublicSteamInventory(): bool
{
    return $this->steamTradeProfile !== null
        && $this->steamTradeProfile->inventory_public === true;
}

public function canUseMarketplace(): bool
{
    return $this->isActive()
        && $this->email_verified_at !== null
        && $this->hasAcceptedMarketplaceTerms()
        && $this->hasVerifiedSteamAccount()
        && $this->hasPublicSteamProfile()
        && $this->hasPublicSteamInventory()
        && $this->hasTradeProfileReady();
}

protected $casts = [
    'email_verified_at' => 'datetime',
    'marketplace_terms_accepted_at' => 'datetime',
    'password' => 'hashed',
];
}
