@extends('layouts.public', [
    'title' => 'How to Find Your Steam Trade URL | CS2 PickLab',
    'pageTitle' => 'How to Find Your Steam Trade URL',
])

@section('content')
<section class="mx-auto max-w-4xl px-6 py-10">
    <div class="mb-8">
        <p class="text-xs font-black uppercase tracking-widest text-cyan-300">
            Steam Marketplace Setup
        </p>

        <h1 class="mt-2 text-4xl font-black text-white">
            How to Find Your Steam Trade URL
        </h1>

        <p class="mt-3 text-slate-400">
            Your Steam Trade URL lets another Steam user send you a trade offer directly.
            CS2 PickLab uses it so accepted marketplace trades can be completed on Steam.
        </p>
    </div>

    <section class="card">
        <h2 class="text-2xl font-black text-white">Step-by-step</h2>

        <ol class="mt-5 space-y-4 text-slate-300">
            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">1. Open Steam in your browser.</strong>
                <p class="mt-1 text-slate-400">
                    Go to Steam and make sure you are signed into the correct account.
                </p>
            </li>

            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">2. Open your Inventory.</strong>
                <p class="mt-1 text-slate-400">
                    Hover over your Steam username, then choose <strong>Inventory</strong>.
                </p>
            </li>

            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">3. Click Trade Offers.</strong>
                <p class="mt-1 text-slate-400">
                    On the Inventory page, click <strong>Trade Offers</strong>.
                </p>
            </li>

            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">4. Click “Who can send me Trade Offers?”</strong>
                <p class="mt-1 text-slate-400">
                    This page contains your personal Steam Trade URL.
                </p>
            </li>

            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">5. Copy your Trade URL.</strong>
                <p class="mt-1 text-slate-400">
                    It should look similar to this:
                </p>

                <code class="mt-3 block overflow-x-auto rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-cyan-200">
                    https://steamcommunity.com/tradeoffer/new/?partner=123456789&token=AbCdEfGh
                </code>
            </li>

            <li class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                <strong class="text-white">6. Paste it into CS2 PickLab.</strong>
                <p class="mt-1 text-slate-400">
                    Return to your Steam marketplace setup page and paste the full URL into the Trade URL field.
                </p>
            </li>
        </ol>
    </section>

    <section class="mt-6 rounded-2xl border border-amber-400/40 bg-amber-400/10 p-5">
        <h2 class="text-xl font-black text-white">Important privacy note</h2>

        <p class="mt-2 text-slate-300">
            Your Steam profile and CS2 inventory must both be public for marketplace selling to work.
            A public profile alone is not enough. Steam inventory privacy is controlled separately.
        </p>
    </section>

    <section class="mt-6 card">
        <h2 class="text-2xl font-black text-white">Quick checklist</h2>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-300">
                Steam account linked
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-300">
                Steam profile set to public
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-300">
                CS2 inventory set to public
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-300">
                Trade URL saved
            </div>
        </div>
    </section>

    <div class="mt-8 flex flex-wrap gap-3">
        @auth
            @if(Route::has('profile.steam'))
                <a href="{{ route('profile.steam') }}" class="btn-primary">
                    Back to Steam Setup
                </a>
            @endif
        @endauth

        <a
            href="https://steamcommunity.com/my/tradeoffers/privacy"
            target="_blank"
            rel="noopener noreferrer"
            class="btn-secondary"
        >
            Open Steam Trade URL Page
        </a>
    </div>
</section>
@endsection