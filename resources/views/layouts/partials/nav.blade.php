@php
    $user = auth()->user();
    $isMarketplaceReady = $user ? $user->canUseMarketplace() : false;
@endphp

<header class="border-b border-slate-800 bg-slate-950/95">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4">
        <a href="{{ route('home') }}" class="shrink-0 text-xl font-black tracking-tight text-white">
            CS2 <span class="text-cyan-400">PickLab</span>
        </a>

        <nav class="flex flex-wrap items-center justify-end gap-3 text-sm font-semibold text-slate-300 md:gap-5">
            {{-- Core public navigation --}}
            <a href="{{ route('marketplace.index') }}" class="rounded-full bg-cyan-400 px-4 py-2 font-black text-slate-950 hover:bg-cyan-300">
                Marketplace
            </a>

            <a href="{{ route('matches.index') }}" class="hover:text-white">Matches</a>
            <a href="{{ route('pickem.index') }}" class="hover:text-white">Pick’em</a>
            <a href="{{ route('teams.index') }}" class="hover:text-white">Teams</a>

            <a href="{{ route('shop.index') }}" class="rounded-full bg-cyan-400 px-4 py-2 font-black text-slate-950 hover:bg-cyan-300">
                Shop
            </a>

            {{-- Marketplace actions for authenticated users --}}
            @auth
                @if ($isMarketplaceReady)
                    @if (Route::has('marketplace.listings.create'))
                        <a
                            href="{{ route('marketplace.listings.create') }}"
                            class="rounded-full border border-cyan-400/40 bg-cyan-400/10 px-4 py-2 font-black text-cyan-200 hover:bg-cyan-400 hover:text-slate-950"
                        >
                            Sell Skins
                        </a>
                    @endif

                    @if (Route::has('marketplace.trade-requests.index'))
                        <a
                            href="{{ route('marketplace.trade-requests.index') }}"
                            class="hover:text-white"
                        >
                            Trade Requests
                        </a>
                    @endif
                @else
                    @if (Route::has('profile.steam'))
                        <a
                            href="{{ route('profile.steam') }}"
                            class="rounded-full border border-amber-400/50 bg-amber-400/10 px-4 py-2 font-black text-amber-200 hover:bg-amber-400 hover:text-slate-950"
                        >
                            Finish Marketplace Setup
                        </a>
                    @endif
                @endif
            @endauth

            {{-- Guest account actions --}}
            @guest
                <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-4 py-2 font-bold text-slate-200 hover:border-slate-500 hover:bg-slate-900 hover:text-white">
                    Sign In
                </a>

                <a href="{{ route('register') }}" class="rounded-full border border-cyan-400/40 bg-cyan-400/10 px-4 py-2 font-black text-cyan-200 hover:bg-cyan-400 hover:text-slate-950">
                    Create Account
                </a>
            @endguest

            {{-- Authenticated account actions --}}
            @auth
                <a
                    href="{{ route('account.show') }}"
                    class="max-w-[220px] truncate rounded-full border border-slate-700 px-4 py-2 font-bold text-slate-200 hover:border-slate-500 hover:bg-slate-900 hover:text-white"
                    title="{{ auth()->user()->email }}"
                >
                    {{ auth()->user()->displayName() ?? auth()->user()->email }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-full border border-slate-700 px-4 py-2 font-bold text-slate-200 hover:border-red-400/60 hover:bg-red-500/10 hover:text-red-200"
                    >
                        Logout
                    </button>
                </form>
            @endauth
        </nav>
    </div>
</header>