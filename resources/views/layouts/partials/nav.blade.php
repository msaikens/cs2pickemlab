<header class="border-b border-slate-800 bg-slate-950/95">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
        <a href="{{ route('home') }}" class="text-xl font-black tracking-tight text-white">
            CS2 <span class="text-cyan-400">PickLab</span>
        </a>

        <nav class="flex items-center gap-5 text-sm font-semibold text-slate-300">
            <a href="{{ route('matches.index') }}" class="hover:text-white">Matches</a>
            <a href="{{ route('pickem.index') }}" class="hover:text-white">Pick’em</a>
            <a href="{{ route('teams.index') }}" class="hover:text-white">Teams</a>
            <a href="{{ route('shop.index') }}" class="rounded-full bg-cyan-400 px-4 py-2 text-slate-950 hover:bg-cyan-300">Shop</a>
        </nav>
    </div>
</header>
