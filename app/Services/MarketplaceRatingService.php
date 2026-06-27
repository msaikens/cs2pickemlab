<?php

namespace App\Services;

use App\Models\MarketplaceRating;
use App\Models\SkinListing;
use App\Models\TradeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class MarketplaceRatingService
{
    public function createRating(
        User $rater,
        User $ratedUser,
        Model $rateable,
        int $rating,
        ?string $comment = null,
    ): MarketplaceRating {
        if ($rater->id === $ratedUser->id) {
            throw ValidationException::withMessages([
                'rated_user_id' => 'You cannot rate yourself.',
            ]);
        }

        if ($rating < 1 || $rating > 5) {
            throw ValidationException::withMessages([
                'rating' => 'Rating must be between 1 and 5 stars.',
            ]);
        }

        if (! $this->canRate($rater, $ratedUser, $rateable)) {
            throw ValidationException::withMessages([
                'rating' => 'You can only rate users after a completed marketplace sale or trade.',
            ]);
        }

        return MarketplaceRating::updateOrCreate(
            [
                'rater_user_id' => $rater->id,
                'rated_user_id' => $ratedUser->id,
                'rateable_type' => $rateable::class,
                'rateable_id' => $rateable->id,
            ],
            [
                'rating' => $rating,
                'comment' => $comment,
            ]
        );
    }

    public function canRate(User $rater, User $ratedUser, Model $rateable): bool
    {
        if (! $this->isCompleted($rateable)) {
            return false;
        }

        $participantIds = $this->participantIds($rateable);

        return in_array($rater->id, $participantIds, true)
            && in_array($ratedUser->id, $participantIds, true);
    }

    private function isCompleted(Model $rateable): bool
    {
        $status = (string) ($rateable->getAttribute('status') ?? '');

        return in_array($status, [
            'completed',
            'complete',
            'fulfilled',
            'closed',
            'sold',
        ], true);
    }

    private function participantIds(Model $rateable): array
    {
        $columns = Schema::getColumnListing($rateable->getTable());

        $possibleColumns = [
            'requester_user_id',
            'recipient_user_id',
            'buyer_user_id',
            'seller_user_id',
            'purchased_by_user_id',
            'accepted_by_user_id',
            'user_id',
            'owner_user_id',
            'trade_partner_user_id',
        ];

        $ids = [];

        foreach ($possibleColumns as $column) {
            if (in_array($column, $columns, true) && $rateable->getAttribute($column)) {
                $ids[] = (int) $rateable->getAttribute($column);
            }
        }

        return array_values(array_unique($ids));
    }
}