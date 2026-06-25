<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ModerationIncident;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountRestrictionController extends Controller
{
    public function banned(Request $request): View
    {
        $user = $request->user();

        $incident = ModerationIncident::query()
            ->with('adminUser')
            ->where('subject_user_id', $user->id)
            ->where('action_type', ModerationIncident::ACTION_BAN)
            ->latest()
            ->first();

        return view('account.restricted.banned', [
            'user' => $user,
            'incident' => $incident,
            'adminName' => $incident?->adminUser?->displayName()
                ?? $user->bannedByUser?->displayName()
                ?? 'an administrator',
            'incidentNumber' => $incident?->incident_number
                ?? $user->site_ban_incident_number
                ?? 'Unavailable',
        ]);
    }

    public function suspended(Request $request): View
    {
        $user = $request->user();

        $incident = ModerationIncident::query()
            ->with('adminUser')
            ->where('subject_user_id', $user->id)
            ->where('action_type', ModerationIncident::ACTION_SUSPENSION)
            ->latest()
            ->first();

        return view('account.restricted.suspended', [
            'user' => $user,
            'incident' => $incident,
            'adminName' => $incident?->adminUser?->displayName()
                ?? $user->suspendedByUser?->displayName()
                ?? 'an administrator',
            'incidentNumber' => $incident?->incident_number
                ?? $user->site_suspension_incident_number
                ?? 'Unavailable',
        ]);
    }
}