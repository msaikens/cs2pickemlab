@extends('layouts.app', ['title' => 'Pick’em Assistant | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-4xl font-black text-white">Pick’em Assistant</h1>

    @if($event)
        <p class="mt-3 text-slate-400">{{ $event->name }} recommendations grouped by Pick’em usage.</p>
    @else
        <p class="mt-3 text-slate-400">No active Pick’em event loaded.</p>
    @endif

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @forelse($recommendations as $slotType => $items)
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                <h2 class="text-2xl font-black text-white">{{ str_replace('_', ' ', strtoupper($slotType)) }}</h2>

                <div class="mt-5 space-y-4">
                    @foreach($items as $rec)
                        <a href="{{ route('teams.show', $rec->team) }}" class="block rounded-xl bg-slate-950 p-4 hover:ring-1 hover:ring-cyan-400">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-bold text-white">{{ $rec->team->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $rec->headline }}</p>
                                </div>
                                <span class="rounded-full bg-slate-800 px-3 py-1 text-sm text-cyan-300">{{ $rec->confidence_score }}%</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-300">{{ $rec->summary }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-slate-400">No recommendations available.</p>
        @endforelse
    </div>
</section>
@endsection
