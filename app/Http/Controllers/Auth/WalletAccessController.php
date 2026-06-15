use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class WalletAccessController extends Controller
{
    public function showConfirm()
    {
        $user = auth()->user();

        return view('wallet.confirm', [
            'hasPassword' => ! empty($user->password),
            'hasTwoFactor' => ! empty($user->two_factor_secret)
                && ! empty($user->two_factor_confirmed_at),
        ]);
    }

    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The password you entered was incorrect.',
            ]);
        }

        $this->confirmWalletAccess();

        return redirect()->intended(route('wallet.index'));
    }

    public function confirmTwoFactor(Request $request, TwoFactorAuthenticationProvider $provider)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();

        if (empty($user->two_factor_secret) || empty($user->two_factor_confirmed_at)) {
            throw ValidationException::withMessages([
                'code' => 'Authenticator verification is not enabled for this account.',
            ]);
        }

        $code = preg_replace('/\s+/', '', $request->input('code'));

        if (! $provider->verify($user->two_factor_secret, $code)) {
            throw ValidationException::withMessages([
                'code' => 'The authenticator code was invalid.',
            ]);
        }

        $this->confirmWalletAccess();

        return redirect()->intended(route('wallet.index'));
    }

    private function confirmWalletAccess(): void
    {
        session([
            'wallet_confirmed_at' => now()->timestamp,
        ]);
    }
}