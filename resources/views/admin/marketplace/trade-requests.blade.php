@extends('layouts.admin', [
    'title' => 'Marketplace Trade Requests',
])

@section('content')
<section class="space-y-6">
    <div>
        <h1 class="text-3xl font-black text-white">Marketplace Trade Requests</h1>
        <p class="mt-2 text-slate-400">Review marketplace trade activity and audit history.</p>
    </div>

    <form method="GET" action="{{ route('admin.marketplace.trade-requests') }}" class="flex flex-wrap gap-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
        <select name="status" class="min-h-11 rounded-xl border border-slate-700 bg-slate-950 px-4 text-white outline-none focus:border-cyan-400">
            <option value="">All Statuses</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
            <option value="declined" @selected(request('status') === 'declined')>Declined</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
        </select>

        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('admin.marketplace.trade-requests') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="grid gap-4">
        @forelse($tradeRequests as $tradeRequest)
            <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">
                            {{ $tradeRequest->listing?->market_hash_name ?? 'Removed Listing' }}
                        </h2>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-xl border border-slate-800 bg-slate-950 p-3">
                                <p class="mb-2 text-xs font-black uppercase tracking-widest text-slate-500">
                                    Buyer
                                </p>

                                @include('components.user-identity', [
                                    'user' => $tradeRequest->buyer,
                                    'size' => 'sm',
                                    'showEmail' => true,
                                    'showAccountType' => true,
                                    'showAccountName' => false,
                                ])
                            </div>

                            <div class="rounded-xl border border-slate-800 bg-slate-950 p-3">
                                <p class="mb-2 text-xs font-black uppercase tracking-widest text-slate-500">
                                    Seller
                                </p>

                                @include('components.user-identity', [
                                    'user' => $tradeRequest->seller,
                                    'size' => 'sm',
                                    'showEmail' => true,
                                    'showAccountType' => true,
                                    'showAccountName' => false,
                                ])
                            </div>
                        </div>
                    </div>

                    <span class="h-fit rounded-full border border-slate-700 px-3 py-1 text-xs font-black uppercase text-slate-200">
                        {{ $tradeRequest->status }}
                    </span>
                </div>

                @if($tradeRequest->message)
                    <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950 p-3 text-slate-300">
                        {{ $tradeRequest->message }}
                    </div>
                @endif

                <div class="mt-4 grid gap-2">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-500">
                        Activity
                    </p>

                    @forelse($tradeRequest->events as $event)
                        <div class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-300">
                            <div class="flex flex-wrap items-center gap-2">
                                <strong class="text-white">
                                    {{ str($event->event_type)->replace('_', ' ')->title() }}
                                </strong>

                                <span class="text-slate-500">by</span>

                                @if($event->actor)
                                    @include('components.user-role-badge', [
                                        'user' => $event->actor,
                                        'showFree' => false,
                                        'showPremium' => true,
                                    ])

                                    <span>{{ $event->actor->displayName() }}</span>
                                @else
                                    <span>System</span>
                                @endif

                                <span class="text-slate-500">
                                    · {{ $event->created_at?->format('M j, Y g:i A') }}
                                </span>
                            </div>

                            @if($event->old_status || $event->new_status)
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $event->old_status ?? 'none' }} → {{ $event->new_status ?? 'none' }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-500">
                            No activity events logged.
                        </div>
                    @endforelse
                </div>
            </article>
        @empty
            <section class="card text-center">
                <h2 class="text-2xl font-black text-white">No trade requests found.</h2>
            </section>
        @endforelse
    </div>

    {{ $tradeRequests->links() }}
</section>
@endsection