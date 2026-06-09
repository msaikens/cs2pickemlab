<footer class="border-t border-slate-800 bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h2 class="text-sm font-black uppercase tracking-widest text-white">
                    Legal
                </h2>

                <nav class="mt-4 space-y-2 text-sm text-slate-500">
                    <a href="{{ route('legal.privacy') }}" class="block hover:text-slate-300">
                        Privacy Policy
                    </a>

                    <a href="{{ route('legal.data') }}" class="block hover:text-slate-300">
                        Data Usage & Collection
                    </a>

                    <a href="{{ route('legal.terms') }}" class="block hover:text-slate-300">
                        Terms of Service
                    </a>

                    <a href="{{ route('legal.affiliate') }}" class="block hover:text-slate-300">
                        Affiliate Disclosures
                    </a>

                    <a href="{{ route('legal.disclaimer') }}" class="block hover:text-slate-300">
                        Disclaimer
                    </a>
                </nav>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-widest text-white">
                    Explore
                </h2>

                <nav class="mt-4 space-y-2 text-sm text-slate-500">
                    <a href="{{ route('home') }}" class="block hover:text-slate-300">
                        Home
                    </a>

                    <a href="{{ route('matches.index') }}" class="block hover:text-slate-300">
                        Matches
                    </a>

                    <a href="{{ route('pickem.index') }}" class="block hover:text-slate-300">
                        Pick’em
                    </a>

                    <a href="{{ route('teams.index') }}" class="block hover:text-slate-300">
                        Teams
                    </a>

                    <a href="{{ route('shop.index') }}" class="block hover:text-slate-300">
                        Shop
                    </a>
                    <a href="{{ route('contact.create') }}" class="block hover:text-slate-300">
                        Contact Us
                    </a>
                </nav>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-widest text-white">
                    CS2 PickLab
                </h2>

                <div class="mt-4 space-y-3 text-sm text-slate-500">
                    <p>
                        CS2 PickLab is an independent fan project. It is not affiliated with Valve, Steam, Counter-Strike, tournament organizers, or professional teams.
                    </p>

                    <p>
                        All content, including match data, team information, and player statistics, is sourced from publicly available information and is intended for entertainment purposes only.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-10 border-t border-slate-800 pt-6 text-xs text-slate-600">
            <p>
                © {{ date('Y') }} CS2 PickLab. All rights reserved.
            </p>
        </div>
    </div>
</footer>