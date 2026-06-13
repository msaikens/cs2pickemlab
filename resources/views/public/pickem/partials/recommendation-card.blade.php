<div class="pickem-recommendation-card">
    <div class="pickem-rec-header">
        <div>
            <strong>{{ $rec->team?->name ?? 'Unknown team' }}</strong>

            <span>
                {{ str_replace('_', ' ', ucfirst($rec->slot_type)) }}
            </span>
        </div>

        <p>{{ $rec->confidence_score }}%</p>
    </div>

    @if(! empty($rec->headline))
        <h3>{{ $rec->headline }}</h3>
    @endif

    @if(! empty($rec->summary))
        <p class="pickem-rec-summary">
            {{ $rec->summary }}
        </p>
    @endif

    <div class="pickem-rec-footer">
        <span>{{ ucfirst($rec->risk_level ?? 'medium') }} risk</span>

        @if($rec->stage)
            <span>{{ $rec->stage->name }}</span>
        @endif
    </div>
</div>