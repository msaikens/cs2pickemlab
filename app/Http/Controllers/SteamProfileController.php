<?php

namespace App\Http\Controllers;

use App\Models\SteamAccount;
use App\Models\SteamTradeProfile;
use App\Services\Marketplace\SteamTradeUrlParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use App\Services\Steam\SteamProfileService;
use RuntimeException;
use App\Models\SkinListing;
use Illuminate\Support\Facades\DB;
use App\Services\Steam\SteamInventoryService;


class SteamProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $user->load(['steamAccount', 'steamTradeProfile']);

        return view('profile.steam', [
            'user' => $user,
            'steamAccount' => $user->steamAccount,
            'steamTradeProfile' => $user->steamTradeProfile,
        ]);
    }

    public function updateTradeUrl(
        Request $request,
        SteamTradeUrlParser $parser
    ): RedirectResponse {
        $validated = $request->validate([
            'steam_trade_url' => ['required', 'url', 'max:2000'],
            'trade_hold_warning_acknowledged' => ['nullable', 'accepted'],
        ]);

        try {
            $parsed = $parser->parse($validated['steam_trade_url']);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withErrors(['steam_trade_url' => $exception->getMessage()])
                ->withInput();
        }

        SteamTradeProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'steam_trade_url' => $validated['steam_trade_url'],
                'trade_partner_id' => $parsed['trade_partner_id'],
                'trade_token' => $parsed['trade_token'],
                'trade_hold_warning_acknowledged_at' => isset($validated['trade_hold_warning_acknowledged'])
                    ? now()
                    : optional($request->user()->steamTradeProfile)->trade_hold_warning_acknowledged_at,
            ]
        );

        return redirect()
            ->route('profile.steam')
            ->with('success', 'Steam trade URL saved.');
    }

    public function mockLink(Request $request): RedirectResponse
    {
        /*
         * Temporary dev-only helper.
         * Remove this when Steam OpenID is connected.
         */
        if (! app()->environment(['local', 'development'])) {
            abort(404);
        }

        $validated = $request->validate([
            'steam_id_64' => ['required', 'string', 'max:32'],
            'persona_name' => ['required', 'string', 'max:255'],
        ]);

        SteamAccount::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'steam_id_64' => $validated['steam_id_64'],
                'persona_name' => $validated['persona_name'],
                'profile_url' => 'https://steamcommunity.com/profiles/' . $validated['steam_id_64'],
                'avatar_url' => null,
                'profile_visibility' => 'mocked',
                'linked_at' => now(),
                'last_verified_at' => now(),
            ]
        );

        return redirect()
            ->route('profile.steam')
            ->with('success', 'Temporary Steam account linked for local testing.');
    }
    public function refreshSteamProfile(
    Request $request,
    SteamProfileService $steamProfileService
): RedirectResponse {
    $steamAccount = $request->user()->steamAccount;

    if (! $steamAccount) {
        return redirect()
            ->route('profile.steam')
            ->with('error', 'No Steam account is linked.');
    }

    try {
        $steamProfile = $steamProfileService->getPlayerSummary($steamAccount->steam_id_64);
    } catch (RuntimeException $exception) {
        return redirect()
            ->route('profile.steam')
            ->with('error', $exception->getMessage());
    }

    $steamAccount->update([
        'persona_name' => $steamProfile['persona_name'],
        'profile_url' => $steamProfile['profile_url'],
        'avatar_url' => $steamProfile['avatar_url'],
        'profile_visibility' => $steamProfile['profile_visibility'],
        'last_verified_at' => now(),
    ]);

    return redirect()
        ->route('profile.steam')
        ->with('success', 'Steam profile refreshed.');
}
public function unlinkSteamAccount(Request $request): RedirectResponse
{
    $user = $request->user();

    DB::transaction(function () use ($user): void {
        /*
         * Deactivate open listings first.
         * We should not leave marketplace listings active after the verified Steam account is removed.
         */
        SkinListing::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'active', 'pending'])
            ->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);

        /*
         * Remove saved Steam trade profile data.
         */
        $user->steamTradeProfile()->delete();

        /*
         * Remove linked Steam identity.
         */
        $user->steamAccount()->delete();
    });

    return redirect()
        ->route('profile.steam')
        ->with('success', 'Your Steam account has been unlinked. Marketplace access is now disabled.');
}
    public function syncInventory(
        Request $request,
        SteamInventoryService $steamInventoryService
        ): RedirectResponse 
        {
            try {
                $synced = $steamInventoryService->syncUserInventory($request->user());
                }   
            catch (RuntimeException $exception) 
                {
                return redirect()
                    ->route('profile.steam')
                    ->with('error', $exception->getMessage());
                }

        return redirect()
            ->route('profile.steam')
            ->with('success', "Steam inventory synced. {$synced} item(s) found.");
    }
}