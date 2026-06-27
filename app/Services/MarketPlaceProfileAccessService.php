<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarketplaceProfileAccessService
{
    public function canViewProfile(?User $viewer, User $target): bool
    {
        if (! $viewer) {
            return false;
        }

        if ($viewer->id === $target->id) {
            return true;
        }

        if (method_exists($viewer, 'isAdmin') && $viewer->isAdmin()) {
            return true;
        }

        return $this->usersHaveMarketplaceRelationship($viewer, $target);
    }

    public function usersHaveMarketplaceRelationship(User $viewer, User $target): bool
    {
        return $this->usersShareTradeRequest($viewer, $target)
            || $this->usersShareSkinListing($viewer, $target);
    }

    private function usersShareTradeRequest(User $viewer, User $target): bool
    {
        if (! Schema::hasTable('trade_requests')) {
            return false;
        }

        $columns = Schema::getColumnListing('trade_requests');

        $possibleUserColumns = collect([
            'requester_user_id',
            'recipient_user_id',
            'buyer_user_id',
            'seller_user_id',
            'user_id',
            'owner_user_id',
            'trade_partner_user_id',
        ])->filter(fn ($column) => in_array($column, $columns, true))->values();

        if ($possibleUserColumns->count() < 2) {
            return false;
        }

        return DB::table('trade_requests')
            ->where(function ($query) use ($possibleUserColumns, $viewer) {
                foreach ($possibleUserColumns as $column) {
                    $query->orWhere($column, $viewer->id);
                }
            })
            ->where(function ($query) use ($possibleUserColumns, $target) {
                foreach ($possibleUserColumns as $column) {
                    $query->orWhere($column, $target->id);
                }
            })
            ->exists();
    }

    private function usersShareSkinListing(User $viewer, User $target): bool
    {
        if (! Schema::hasTable('skin_listings')) {
            return false;
        }

        $columns = Schema::getColumnListing('skin_listings');

        $sellerColumns = collect([
            'seller_user_id',
            'user_id',
            'owner_user_id',
        ])->filter(fn ($column) => in_array($column, $columns, true))->values();

        $buyerColumns = collect([
            'buyer_user_id',
            'purchased_by_user_id',
            'accepted_by_user_id',
        ])->filter(fn ($column) => in_array($column, $columns, true))->values();

        if ($sellerColumns->isEmpty() || $buyerColumns->isEmpty()) {
            return false;
        }

        return DB::table('skin_listings')
            ->where(function ($query) use ($sellerColumns, $buyerColumns, $viewer) {
                foreach ($sellerColumns->merge($buyerColumns) as $column) {
                    $query->orWhere($column, $viewer->id);
                }
            })
            ->where(function ($query) use ($sellerColumns, $buyerColumns, $target) {
                foreach ($sellerColumns->merge($buyerColumns) as $column) {
                    $query->orWhere($column, $target->id);
                }
            })
            ->exists();
    }
}