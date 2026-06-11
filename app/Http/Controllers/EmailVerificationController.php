<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): RedirectResponse
    {
        return redirect()
            ->route('account.show')
            ->with('error', 'Please verify your email address before continuing.');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403);
        abort_unless($request->hasValidSignature(), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return redirect()
            ->route('account.show')
            ->with('success', 'E-mail successfully verified.');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'verification_code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'E-mail is already verified.');
        }

        $code = $user->emailVerificationCodes()
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $code) {
            return back()->with('error', 'No active verification code was found. Send a new verification email.');
        }

        if ($code->isExpired()) {
            return back()->with('error', 'That verification code has expired. Send a new verification email.');
        }

        if (! hash_equals($code->code_hash, hash('sha256', $validated['verification_code']))) {
            return back()->with('error', 'That verification code is incorrect.');
        }

        $code->update([
            'consumed_at' => now(),
        ]);

        $user->markEmailAsVerified();

        event(new Verified($user));

        return redirect()
            ->route('account.show')
            ->with('success', 'E-mail successfully verified.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'E-mail is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent.');
    }
}