<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WalletAccessCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Illuminate\View\View;

class ConfirmPasswordController extends Controller
{
    public function create(Request $request)
{
    $user = $request->user();

    return view('auth.confirm-password', [
        'user' => $user,
        'hasPassword' => ! empty($user->password),
        'hasTwoFactor' => ! empty($user->two_factor_secret)
            && ! empty($user->two_factor_confirmed_at),
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (filled($user->password)) {
            $request->validate([
                'password' => ['required', 'string'],
            ]);

            if (! Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'The provided password was incorrect.',
                ]);
            }

            $request->session()->put('auth.password_confirmed_at', time());

            return redirect()->intended(route('account.wallet'));
        }

        $code = (string) random_int(100000, 999999);

        Session::put('wallet_access_code_hash', Hash::make($code));
        Session::put('wallet_access_code_expires_at', now()->addMinutes(10)->timestamp);

        Mail::to($user->email)->send(new WalletAccessCodeMail($code));

        return redirect()
            ->route('password.confirm.code')
            ->with('status', 'We sent a wallet access code to your email.');
    }

    public function code(): View
    {
        return view('auth.confirm-password-code');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $expiresAt = Session::get('wallet_access_code_expires_at');
        $codeHash = Session::get('wallet_access_code_hash');

        if (! $expiresAt || ! $codeHash || now()->timestamp > $expiresAt) {
            return redirect()
                ->route('password.confirm')
                ->withErrors([
                    'code' => 'Your wallet access code expired. Send a new code.',
                ]);
        }

        if (! Hash::check($request->code, $codeHash)) {
            return back()->withErrors([
                'code' => 'The wallet access code was incorrect.',
            ]);
        }

        Session::forget([
            'wallet_access_code_hash',
            'wallet_access_code_expires_at',
        ]);

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('account.wallet'));
    }
    public function confirmTwoFactor(
    Request $request,
    TwoFactorAuthenticationProvider $provider
) {
    $request->validate([
        'code' => ['required', 'string', 'max:20'],
    ]);

    $user = $request->user();

    if (
        empty($user->two_factor_secret)
        || empty($user->two_factor_confirmed_at)
    ) {
        throw ValidationException::withMessages([
            'code' => 'Authenticator verification is not enabled for this account.',
        ]);
    }

    $code = preg_replace('/\s+/', '', $request->input('code'));

    if (! $provider->verify(Crypt::decrypt($user->two_factor_secret), $code)) {
        throw ValidationException::withMessages([
            'code' => 'The authenticator code was invalid.',
        ]);
    }

    session([
        'auth.password_confirmed_at' => time(),
        'wallet_confirmed_at' => time(),
    ]);

    return redirect()->intended(route('account.wallet'));
}
}