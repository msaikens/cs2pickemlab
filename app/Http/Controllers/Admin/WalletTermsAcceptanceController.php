<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTermsAcceptance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletTermsAcceptanceController extends Controller
{
    public function index(Request $request): View
    {
        $currentVersion = WalletTermsAcceptance::currentTermsVersion();

        $acceptances = WalletTermsAcceptance::query()
            ->with(['user.profile', 'user.steamAccount'])
            ->when($request->filled('version'), function ($query) use ($request) {
                $query->where('terms_version', $request->string('version'));
            })
            ->when($request->filled('source'), function ($query) use ($request) {
                $query->where('source', $request->string('source'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));

                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('terms_version', 'like', "%{$search}%")
                        ->orWhere('source', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('accepted_at')
            ->paginate(25)
            ->withQueryString();

        $versions = WalletTermsAcceptance::query()
            ->select('terms_version')
            ->distinct()
            ->orderByDesc('terms_version')
            ->pluck('terms_version');

        $sources = WalletTermsAcceptance::query()
            ->select('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        $stats = [
            'total' => WalletTermsAcceptance::count(),
            'current_version' => WalletTermsAcceptance::where('terms_version', $currentVersion)->count(),
            'unique_users' => WalletTermsAcceptance::distinct('user_id')->count('user_id'),
            'latest_acceptance' => WalletTermsAcceptance::latest('accepted_at')->value('accepted_at'),
        ];

        return view('admin.wallet-terms.acceptances', [
            'acceptances' => $acceptances,
            'versions' => $versions,
            'sources' => $sources,
            'stats' => $stats,
            'currentVersion' => $currentVersion,
        ]);
    }
}