@php
    $title = $title ?? 'Admin | CS2 PickLab';
    $pageTitle = $pageTitle ?? 'Admin';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-800 bg-slate-900/95 p-6 lg:block">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <div class="text-2xl font-black tracking-tight text-white">
                    CS2 PickLab
                </div>
                <div class="mt-1 text-xs font-bold uppercase tracking-widest text-cyan-400">
                    Admin Panel
                </div>
            </a>

            <nav class="mt-8 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Dashboard
                </a>

                <a href="{{ route('admin.teams.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Teams
                </a>

                <a href="{{ route('admin.players.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Players
                </a>

                <a href="{{ route('admin.events.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Events
                </a>

                <a href="{{ route('admin.matches.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Matches
                </a>

                <a href="{{ route('admin.predictions.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Predictions
                </a>

                <a href="{{ route('admin.pickem.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Pick’em
                </a>

                <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Products
                </a>

                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Orders
                </a>
                
                <a href="{{ route('admin.marketplace.listings') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Marketplace Listings
                </a>

                <a href="{{ route('admin.marketplace.trade-requests') }}" class="block rounded-lg px-4 py-2 font-bold text-slate-200 hover:bg-slate-800 hover:text-white">
                    Marketplace Trades
                </a>
            </nav>
        </aside>

        <main class="lg:pl-72">
            <header class="border-b border-slate-800 bg-slate-900/80 px-6 py-5">
                <div class="mx-auto max-w-7xl">
                    <h1 class="text-3xl font-black text-white">
                        {{ $pageTitle }}
                    </h1>
                </div>
            </header>

            <section class="mx-auto max-w-7xl px-6 py-8">
                @if(session('success'))
                    <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 font-bold text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 font-bold text-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
