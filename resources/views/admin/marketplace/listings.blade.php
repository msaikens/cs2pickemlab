@extends('layouts.admin', [
    'title' => 'Marketplace Listings',
])

@section('content')
<section class="space-y-6">
    <div>
        <h1 class="text-3xl font-black text-white">Marketplace Listings</h1>
        <p class="mt-2 text-slate-400">Review, search, and moderate user skin listings.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.marketplace.listings') }}" class="flex flex-wrap gap-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="Search listing, asset ID, user..."
            class="min-h-11 flex-1 rounded-xl border border-slate-700 bg-slate-950 px-4 text-white outline-none focus:border-cyan-400"
        >

        <select name="status" class="min-h-11 rounded-xl border border-slate-700 bg-slate-950 px-4 text-white outline-none focus:border-cyan-400">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>

        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('admin.marketplace.listings') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-800">
        <table class="w-full divide-y divide-slate-800 text-left text-sm">
            <thead class="bg-slate-900 text-xs uppercase tracking-widest text-slate-400">
                <tr>
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3">Seller</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Requests</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800 bg-slate-950">
                @forelse($listings as $listing)
                    <tr>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                @if($listing->image_url)
                                    <img
                                        src="{{ $listing->image_url }}"
                                        alt="{{ $listing->market_hash_name }}"
                                        class="h-12 w-12 rounded-lg object-contain"
                                    >
                                @endif

                                <div>
                                    <p class="font-bold text-white">{{ $listing->market_hash_name }}</p>
                                    <p class="text-xs text-slate-500">Asset: {{ $listing->steam_asset_id ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            @include('components.user-identity', [
                                'user' => $listing->user,
                                'size' => 'sm',
                                'showEmail' => true,
                                'showAccountType' => true,
                                'showAccountName' => false,
                            ])
                        </td>

                        <td class="px-4 py-4">
                            <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-black uppercase text-slate-200">
                                {{ $listing->status }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-slate-300">{{ $listing->display_price }}</td>

                        <td class="px-4 py-4 text-slate-300">{{ $listing->tradeRequests->count() }}</td>

                        <td class="px-4 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('marketplace.listings.show', $listing) }}" class="btn-secondary">
                                    View
                                </a>

                                @if(in_array($listing->status, ['draft', 'active', 'pending'], true))
                                    <form
                                        method="POST"
                                        action="{{ route('admin.marketplace.listings.cancel', $listing) }}"
                                        onsubmit="return confirm('Cancel this listing and open requests?');"
                                    >
                                        @csrf

                                        <button type="submit" class="btn-danger">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                            No marketplace listings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $listings->links() }}
</section>
@endsection