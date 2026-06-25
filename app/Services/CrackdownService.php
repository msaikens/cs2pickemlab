<?php

namespace App\Services;

use App\Models\ModerationIncident;
use App\Models\SkinListing;
use App\Models\User;
use App\Models\UserInboxMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use App\Models\ModerationAppeal;
use Illuminate\Support\Carbon;

class CrackdownService
{
    public function warn(User $subjectUser, User $adminUser, array $data): ModerationIncident
    {
        $this->guardAdminCannotActOnSelf($subjectUser, $adminUser);

        return DB::transaction(function () use ($subjectUser, $adminUser, $data) {
            return $this->createIncidentWithInboxMessage(
                subjectUser: $subjectUser,
                adminUser: $adminUser,
                actionType: ModerationIncident::ACTION_WARNING,
                title: $data['title'] ?? 'Account warning',
                userMessage: $data['user_message'],
                adminNote: $data['admin_note'] ?? null,
            );
        });
    }

    public function suspend(User $subjectUser, User $adminUser, array $data): ModerationIncident
    {
        $this->guardAdminCannotActOnSelf($subjectUser, $adminUser);

        return DB::transaction(function () use ($subjectUser, $adminUser, $data) {
            $incident = $this->createIncidentWithInboxMessage(
                subjectUser: $subjectUser,
                adminUser: $adminUser,
                actionType: ModerationIncident::ACTION_SUSPENSION,
                title: $data['title'] ?? 'Account suspended',
                userMessage: $data['user_message'],
                adminNote: $data['admin_note'] ?? null,
                endsAt: $data['ends_at'],
            );

            $subjectUser->forceFill([
                'site_suspended_until' => $data['ends_at'],
                'site_suspended_by_user_id' => $adminUser->id,
                'site_suspension_incident_number' => $incident->incident_number,
            ])->save();

            return $incident;
        });
    }

    public function ban(User $subjectUser, User $adminUser, array $data): ModerationIncident
    {
        $this->guardAdminCannotActOnSelf($subjectUser, $adminUser);

        return DB::transaction(function () use ($subjectUser, $adminUser, $data) {
            $incident = $this->createIncidentWithInboxMessage(
                subjectUser: $subjectUser,
                adminUser: $adminUser,
                actionType: ModerationIncident::ACTION_BAN,
                title: $data['title'] ?? 'Account banned',
                userMessage: $data['user_message'],
                adminNote: $data['admin_note'] ?? null,
            );

            $subjectUser->forceFill([
                'site_banned_at' => now(),
                'site_banned_by_user_id' => $adminUser->id,
                'site_ban_incident_number' => $incident->incident_number,
            ])->save();

            $this->removeActiveListingsForUser(
                subjectUser: $subjectUser,
                reason: 'User banned by admin action.',
            );

            return $incident;
        });
    }

    public function removeListings(User $subjectUser, User $adminUser, array $data): ModerationIncident
    {
        $this->guardAdminCannotActOnSelf($subjectUser, $adminUser);

        return DB::transaction(function () use ($subjectUser, $adminUser, $data) {
            $removedCount = $this->removeActiveListingsForUser(
                subjectUser: $subjectUser,
                reason: $data['admin_note'] ?? 'Listings removed by admin action.',
            );

            return $this->createIncidentWithInboxMessage(
                subjectUser: $subjectUser,
                adminUser: $adminUser,
                actionType: ModerationIncident::ACTION_LISTINGS_REMOVED,
                title: $data['title'] ?? 'Marketplace listings removed',
                userMessage: $data['user_message'],
                adminNote: $data['admin_note'] ?? null,
                listingsRemovedCount: $removedCount,
            );
        });
    }

    private function createIncidentWithInboxMessage(
        User $subjectUser,
        User $adminUser,
        string $actionType,
        string $title,
        string $userMessage,
        ?string $adminNote = null,
        mixed $endsAt = null,
        int $listingsRemovedCount = 0,
    ): ModerationIncident {
        $incident = ModerationIncident::create([
            'incident_number' => ModerationIncident::generateIncidentNumber(),
            'subject_user_id' => $subjectUser->id,
            'admin_user_id' => $adminUser->id,
            'action_type' => $actionType,
            'status' => ModerationIncident::STATUS_ACTIVE,
            'title' => $title,
            'user_message' => $userMessage,
            'admin_note' => $adminNote,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'listings_removed_count' => $listingsRemovedCount,
        ]);

        UserInboxMessage::create([
            'user_id' => $subjectUser->id,
            'moderation_incident_id' => $incident->id,
            'type' => UserInboxMessage::TYPE_MODERATION,
            'title' => $title,
            'body' => $userMessage . "\n\nIncident number: " . $incident->incident_number,
        ]);

        return $incident;
    }

    private function removeActiveListingsForUser(User $subjectUser, ?string $reason = null): int
    {
        $query = SkinListing::query()
            ->where('user_id', $subjectUser->id)
            ->whereIn('status', ['active', 'pending']);

        $removedCount = (clone $query)->count();

        if ($removedCount === 0) {
            return 0;
        }

        $updates = [
            'status' => 'cancelled',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('skin_listings', 'cancelled_at')) {
            $updates['cancelled_at'] = now();
        }

        if (Schema::hasColumn('skin_listings', 'admin_note')) {
            $updates['admin_note'] = $reason;
        }

        $query->update($updates);

        return $removedCount;
    }

    private function guardAdminCannotActOnSelf(User $subjectUser, User $adminUser): void
    {
        if ($subjectUser->id === $adminUser->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot issue moderation actions against your own account.',
            ]);
        }
    }
    public function appealIncident(User $user, ModerationIncident $incident, string $message): ModerationAppeal
    {
        if ($incident->subject_user_id !== $user->id) {
            throw ValidationException::withMessages([
                'incident' => 'You can only appeal incidents issued to your own account.',
            ]);
        }

        if (! $incident->canBeAppealed()) {
            throw ValidationException::withMessages([
                'incident' => 'This incident cannot be appealed.',
            ]);
        }

        $existingPendingAppeal = ModerationAppeal::query()
            ->where('moderation_incident_id', $incident->id)
            ->where('user_id', $user->id)
            ->where('status', ModerationAppeal::STATUS_PENDING)
            ->exists();

        if ($existingPendingAppeal) {
            throw ValidationException::withMessages([
                'incident' => 'You already have a pending appeal for this incident.',
            ]);
        }

        return ModerationAppeal::create([
            'moderation_incident_id' => $incident->id,
            'user_id' => $user->id,
            'status' => ModerationAppeal::STATUS_PENDING,
            'message' => $message,
        ]);
    }

    public function reverseIncident(
        ModerationIncident $incident,
        User $adminUser,
        string $reason,
        ?ModerationAppeal $appeal = null
    ): ModerationIncident {
        return DB::transaction(function () use ($incident, $adminUser, $reason, $appeal) {
            $incident->loadMissing('subjectUser');

            if ($incident->isReversed()) {
                throw ValidationException::withMessages([
                    'incident' => 'This incident has already been reversed.',
                ]);
            }

            $subjectUser = $incident->subjectUser;

            if (! $subjectUser) {
                throw ValidationException::withMessages([
                    'incident' => 'The user for this incident no longer exists.',
                ]);
            }

            match ($incident->action_type) {
                ModerationIncident::ACTION_BAN => $this->reverseBan($subjectUser, $incident),
                ModerationIncident::ACTION_SUSPENSION => $this->reverseSuspension($subjectUser, $incident),
                default => null,
            };

            $incident->forceFill([
                'status' => ModerationIncident::STATUS_REVERSED,
                'reversed_at' => now(),
                'reversed_by_user_id' => $adminUser->id,
                'reversal_reason' => $reason,
                'resolved_at' => now(),
            ])->save();

            if ($appeal) {
                $appeal->forceFill([
                    'status' => ModerationAppeal::STATUS_APPROVED,
                    'reviewed_by_user_id' => $adminUser->id,
                    'reviewed_at' => now(),
                    'review_note' => $reason,
                ])->save();
            }

            UserInboxMessage::create([
                'user_id' => $subjectUser->id,
                'moderation_incident_id' => $incident->id,
                'type' => UserInboxMessage::TYPE_MODERATION,
                'title' => 'Moderation action reversed',
                'body' => "A moderation action on your account has been reversed.\n\nIncident number: {$incident->incident_number}\n\nReason: {$reason}",
            ]);

            return $incident->fresh();
        });
    }

    public function denyAppeal(ModerationAppeal $appeal, User $adminUser, string $reviewNote): ModerationAppeal
    {
        return DB::transaction(function () use ($appeal, $adminUser, $reviewNote) {
            if (! $appeal->isPending()) {
                throw ValidationException::withMessages([
                    'appeal' => 'This appeal has already been reviewed.',
                ]);
            }

            $appeal->loadMissing(['user', 'incident']);

            $appeal->forceFill([
                'status' => ModerationAppeal::STATUS_DENIED,
                'reviewed_by_user_id' => $adminUser->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ])->save();

            UserInboxMessage::create([
                'user_id' => $appeal->user_id,
                'moderation_incident_id' => $appeal->moderation_incident_id,
                'type' => UserInboxMessage::TYPE_MODERATION,
                'title' => 'Moderation appeal reviewed',
                'body' => "Your appeal was reviewed and denied.\n\nIncident number: {$appeal->incident?->incident_number}\n\nReason: {$reviewNote}",
            ]);

            return $appeal->fresh();
        });
    }

    private function reverseBan(User $subjectUser, ModerationIncident $incident): void
    {
        if ($subjectUser->site_ban_incident_number !== $incident->incident_number) {
            return;
        }

        $subjectUser->forceFill([
            'site_banned_at' => null,
            'site_banned_by_user_id' => null,
            'site_ban_incident_number' => null,
        ])->save();
    }

    private function reverseSuspension(User $subjectUser, ModerationIncident $incident): void
    {
        if ($subjectUser->site_suspension_incident_number !== $incident->incident_number) {
            return;
        }

        $subjectUser->forceFill([
            'site_suspended_until' => null,
            'site_suspended_by_user_id' => null,
            'site_suspension_incident_number' => null,
        ])->save();
    }
}