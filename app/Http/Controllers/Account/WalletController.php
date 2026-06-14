<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $wallet = $user->wallet()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'currency' => config('services.stripe.currency', 'usd'),
            'available_balance_cents' => 0,
            'pending_balance_cents' => 0,
        ]);

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);

        return view('account.wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}