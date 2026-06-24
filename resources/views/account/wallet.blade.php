@extends('layouts.public', [
    'title' => 'Wallet | CS2 PickLab',
    'pageTitle' => 'Wallet',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-wallet.css') }}">
@endpush

@section('content')

<section class="wallet-page">
    <header class="wallet-hero">
        <div>
            <p class="wallet-kicker">Account Wallet</p>
            <h1>Wallet</h1>
            <p>Your private CS2 PickLab balance and wallet activity.</p>
        </div>

        <a href="{{ route('account.show') }}" class="wallet-button secondary">
            Back to Account
        </a>
    </header>

    @if(session('status'))
        <div class="wallet-alert">
            {{ session('status') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="wallet-alert">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="wallet-alert">
            {{ session('error') }}
        </div>
    @endif

    <section class="wallet-balance-grid">
        <article class="wallet-balance-card primary">
            <p>Available Balance</p>
            <strong>${{ $wallet->available_balance_dollars }}</strong>
            <span>Available for marketplace purchases.</span>
        </article>

        <article class="wallet-balance-card">
            <p>Pending Balance</p>
            <strong>${{ $wallet->pending_balance_dollars }}</strong>
            <span>Pending seller funds or unsettled activity.</span>
        </article>

        <article class="wallet-balance-card">
            <p>Reserved Balance</p>
            <strong>${{ $wallet->reserved_balance_dollars }}</strong>
            <span>Funds locked in active marketplace trade requests.</span>
        </article>
    </section>

    <section class="wallet-topup-card">
        <div class="wallet-terms-status {{ $hasAcceptedWalletTerms ? 'accepted' : 'missing' }}">
            <div>
            <strong>Wallet Terms</strong>

                @if($hasAcceptedWalletTerms)
                    <span>
                        Accepted version {{ $walletTermsVersion }}
                            @if($walletTermsAcceptance?->accepted_at)
                                on {{ $walletTermsAcceptance->accepted_at->format('M j, Y g:i A') }}
                            @endif
                    </span>
                @else
                <span>
                    Acceptance required before wallet top-ups and protected marketplace actions.
                </span>
                @endif
            </div>

            <a href="{{ route('wallet.terms', ['source' => \App\Models\WalletTermsAcceptance::SOURCE_TOP_UP_GATE]) }}">
                {{ $hasAcceptedWalletTerms ? 'Review Terms' : 'Accept Terms' }}
            </a>
        </div>
        <div>
            <h2>Add Funds</h2>
            <p>
                Use Stripe checkout to add spendable site credit to your wallet.
                Top-up funds are not directly withdrawable.
            </p>
        </div>

        @unless($hasAcceptedWalletTerms)
            <div class="wallet-alert">
                You must review and accept the current Wallet Terms before adding funds.
                <a href="{{ route('wallet.terms', ['source' => \App\Models\WalletTermsAcceptance::SOURCE_TOP_UP_GATE]) }}">
                    Review Wallet Terms
                </a>
            </div>
        @endunless

        <form method="POST" action="{{ route('wallet.topup.create') }}" class="wallet-topup-form">
            @csrf

            <label for="amount_dollars">Amount</label>

            <div class="wallet-topup-row">
                <input
                    id="amount_dollars"
                    name="amount_dollars"
                    type="number"
                    min="5"
                    max="500"
                    step="0.01"
                    value="{{ old('amount_dollars', '5.00') }}"
                    required
                >

                <button type="submit">
                    Add Funds
                </button>
            </div>

            @error('amount_dollars')
                <p class="wallet-error">{{ $message }}</p>
            @enderror
        </form>

        <p class="wallet-help-text">
            Seller proceeds are tracked separately and may become withdrawable only after marketplace review, settlement, and any applicable holds.
            Unused eligible top-up funds may be refunded to the original payment method after review.
        </p>
    </section>

    <section class="wallet-transactions-card">
        <div class="wallet-section-header">
            <div>
                <h2>Recent Activity</h2>
                <p>Your latest wallet transactions.</p>
            </div>
        </div>

        <div class="wallet-table-wrap">
            <table class="wallet-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="numeric">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at?->format('M j, Y g:i A') }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($transaction->type)) }}</td>
                            <td>
                                <span class="wallet-status">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="numeric">
                                ${{ number_format($transaction->amount_cents / 100, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="wallet-empty">
                                No wallet activity yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $transactions->links() }}
    </section>
</section>
@endsection