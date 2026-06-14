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
    </section>

    <section class="wallet-topup-card">
        <div>
            <h2>Add Funds</h2>
            <p>Use Stripe test checkout to add funds to your wallet.</p>
        </div>

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
                    value="5.00"
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