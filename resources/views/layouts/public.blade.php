@php
    $title = $title ?? 'CS2 PickLab';
    $pageTitle = $pageTitle ?? null;
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
        <header class="border-b border-slate-800 bg-slate-900/90">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                <a href="{{ route('home') }}" class="block">
                    <div class="text-2xl font-black tracking-tight text-white">
                        CS2 PickLab
                    </div>
                    <div class="mt-1 text-xs font-bold uppercase tracking-widest text-cyan-400">
                        Pick’em · Matches · Teams · Shop
                    </div>
                </a>

                <nav class="flex flex-wrap items-center gap-3 text-sm font-bold">
                    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">
                        Home
                    </a>

                    <a href="{{ route('matches.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">
                        Matches
                    </a>

                    <a href="{{ route('teams.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">
                        Teams
                    </a>

                    <a href="{{ route('pickem.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">
                        Pick’em
                    </a>

                    <a href="{{ route('shop.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">
                        Shop
                    </a>

                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-cyan-400/40 bg-cyan-400/10 px-3 py-2 text-cyan-200 hover:bg-cyan-400 hover:text-slate-950">
                        Admin
                    </a>
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="mt-12 border-t border-slate-800 bg-slate-900/60">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>
                    © {{ date('Y') }} CS2 PickLab
                </p>

                <p>
                    CS2 Pick’em tools, match tracking, and recommendations.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
