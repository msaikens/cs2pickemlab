<?php

namespace App\Http\Controllers;

use App\Models\SteamAccount;
use App\Services\Steam\SteamProfileService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SteamOpenIdController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $returnTo = route('profile.steam.callback', [], true);
        $realm = rtrim(config('app.url'), '/');

        $query = http_build_query([
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $returnTo,
            'openid.realm' => $realm,
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ]);

        return redirect()->away(config('services.steam.openid_url') . '?' . $query);
    }

    public function callback(Request $request, SteamProfileService $steamProfileService): RedirectResponse
    {
        $openidMode = $request->query('openid_mode');

        if ($openidMode === 'cancel') {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Steam linking was cancelled.');
        }

        if ($openidMode !== 'id_res') {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Steam did not return a valid login response.');
        }

        if (! $this->verifySteamOpenId($request)) {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Steam verification failed. Please try again.');
        }

        $claimedId = $request->query('openid_claimed_id');
        $steamId64 = $this->extractSteamId64($claimedId);

        if (! $steamId64) {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Could not read SteamID64 from Steam response.');
        }

        $existingLinkedAccount = SteamAccount::where('steam_id_64', $steamId64)
            ->where('user_id', '!=', $request->user()->id)
            ->first();

        if ($existingLinkedAccount) {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'That Steam account is already linked to another user.');
        }

        try {
            $steamProfile = $steamProfileService->getPlayerSummary($steamId64);
        } catch (RuntimeException) {
            $steamProfile = [
                'steam_id_64' => $steamId64,
                'persona_name' => null,
                'profile_url' => 'https://steamcommunity.com/profiles/' . $steamId64,
                'avatar_url' => null,
                'profile_visibility' => null,
            ];
        }

        SteamAccount::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'steam_id_64' => $steamProfile['steam_id_64'],
                'persona_name' => $steamProfile['persona_name'],
                'profile_url' => $steamProfile['profile_url'],
                'avatar_url' => $steamProfile['avatar_url'],
                'profile_visibility' => $steamProfile['profile_visibility'],
                'linked_at' => now(),
                'last_verified_at' => now(),
            ]
        );

        return redirect()
            ->route('profile.steam')
            ->with('success', 'Steam account linked successfully.');
    }

    private function verifySteamOpenId(Request $request): bool
    {
        $params = $this->convertLaravelOpenIdQueryToSteamParams($request);

        if (empty($params['openid.assoc_handle']) || empty($params['openid.signed']) || empty($params['openid.sig'])) {
            return false;
        }

        $params['openid.mode'] = 'check_authentication';

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post(config('services.steam.openid_url'), $params)
                ->throw();
        } catch (RequestException) {
            return false;
        }

        return str_contains($response->body(), 'is_valid:true');
    }

    private function convertLaravelOpenIdQueryToSteamParams(Request $request): array
    {
        $params = [];

        foreach ($request->query() as $key => $value) {
            if (! str_starts_with($key, 'openid_')) {
                continue;
            }

            $steamKey = str_replace('openid_', 'openid.', $key);

            $params[$steamKey] = $value;
        }

        return $params;
    }

    private function extractSteamId64(?string $claimedId): ?string
    {
        if (! $claimedId) {
            return null;
        }

        if (! preg_match('#^https://steamcommunity\.com/openid/id/(\d{17})$#', $claimedId, $matches)) {
            return null;
        }

        return $matches[1];
    }
}