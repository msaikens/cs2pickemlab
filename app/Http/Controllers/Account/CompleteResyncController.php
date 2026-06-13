<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\Users\UserFootprintResyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompleteResyncController extends Controller
{
    public function __invoke(Request $request, UserFootprintResyncService $resyncService): RedirectResponse
    {
        $user = $request->user();

        $results = $resyncService->resync($user);

        $message = 'Complete re-sync finished.';

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