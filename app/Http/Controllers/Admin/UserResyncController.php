<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Users\UserFootprintResyncService;
use Illuminate\Http\RedirectResponse;

class UserResyncController extends Controller
{
    public function resync(User $user, UserFootprintResyncService $resyncService): RedirectResponse
    {
        $results = $resyncService->resync($user);

        $message = 'Complete re-sync finished for ' . $user->displayName() . '.';

        if ($results['inventory_synced']) {
            $message .= ' Inventory synced: ' . number_format($results['inventory_count']) . ' item(s).';
        }

        if (! empty($results['warnings'])) {
            return back()
                ->with('success', $message)
                ->with('error', implode(' ', $results['warnings']));
        }

        return back()->with('success', $message);
    }
}