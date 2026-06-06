<div class="rounded-xl border border-slate-800 bg-slate-950 p-4">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="font-black text-white">
                {{ $rec->team?->name ?? 'Unknown team' }}
            </p>

            <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-500">
                {{ str_replace('_', ' ', ucfirst($rec->slot_type)) }}
            </p>
        </div>

        <span class="rounded-full border border-cyan-400/40 bg-cyan-400/10 px-2 py-1 text-xs font-black text-cyan-200">
            {{ $rec->confidence_score }}%
        </span>
    </div>

    @if(! empty($rec->headline))
        <p class="mt-3 font-bold text-slate-200">
            {{ $rec->headline }}
        </p>
    @endif

    @if(! empty($rec->summary))
        <p class="mt-2 text-sm text-slate-400">
            {{ $rec->summary }}
        </p>
    @endif

    <div class="mt-3 flex items-center justify-between gap-3 text-xs font-bold uppercase tracking-widest text-slate-500">
        <span>{{ ucfirst($rec->risk_level ?? 'medium') }} risk</span>

        @if($rec->stage)
            <span>{{ $rec->stage->name }}</span>
        @endif
    </div>
</div>
