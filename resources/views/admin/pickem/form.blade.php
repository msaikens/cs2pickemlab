<div class="form-grid">
    <div>
        <label class="form-label" for="event_id">Event</label>
        <select id="event_id" name="event_id" class="form-input" required>
            <option value="">Choose event</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" @selected((string) old('event_id', $recommendation->event_id) === (string) $event->id)>
                    {{ $event->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="event_stage_id">Stage</label>
        <select id="event_stage_id" name="event_stage_id" class="form-input">
            <option value="">No specific stage</option>
            @foreach($stages as $stage)
                <option value="{{ $stage->id }}" @selected((string) old('event_stage_id', $recommendation->event_stage_id) === (string) $stage->id)>
                    {{ $stage->event?->name }} — {{ $stage->name }}
                </option>
            @endforeach
        </select>
        <p class="form-help">For now this lists all Pick’em-enabled stages. We’ll make it dynamic later.</p>
    </div>

    <div>
        <label class="form-label" for="team_id">Team</label>
        <select id="team_id" name="team_id" class="form-input" required>
            <option value="">Choose team</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('team_id', $recommendation->team_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="slot_type">Slot Type</label>
        <select id="slot_type" name="slot_type" class="form-input" required>
            @foreach([
                'safe_3_0' => 'Safe 3:0',
                'risky_3_0' => 'Risky 3:0',
                'safe_advancement' => 'Safe Advancement',
                'risky_advancement' => 'Risky Advancement',
                'likely_0_3' => 'Likely 0:3',
                'upset_watch' => 'Upset Watch',
                'avoid' => 'Avoid',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('slot_type', $recommendation->slot_type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="risk_level">Risk Level</label>
        <select id="risk_level" name="risk_level" class="form-input" required>
            @foreach([
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('risk_level', $recommendation->risk_level) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="confidence_score">Confidence Score</label>
        <input
            id="confidence_score"
            name="confidence_score"
            type="number"
            min="0"
            max="100"
            value="{{ old('confidence_score', $recommendation->confidence_score ?? 50) }}"
            class="form-input"
            required
        >
    </div>

    <div>
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $recommendation->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="sort_order">Sort Order</label>
        <input
            id="sort_order"
            name="sort_order"
            type="number"
            value="{{ old('sort_order', $recommendation->sort_order ?? 0) }}"
            class="form-input"
        >
    </div>
</div>

<label class="checkbox-card">
    <input
        type="checkbox"
        name="is_premium"
        value="1"
        @checked(old('is_premium', $recommendation->is_premium))
        class="checkbox-input"
    >
    <span>
        <span class="block font-bold text-white">Premium Recommendation</span>
        <span class="block text-xs text-slate-500">Hide full reasoning later behind subscription access.</span>
    </span>
</label>

<div>
    <label class="form-label" for="headline">Headline</label>
    <input
        id="headline"
        name="headline"
        type="text"
        value="{{ old('headline', $recommendation->headline) }}"
        placeholder="Safe advancement pick"
        class="form-input"
    >
</div>

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public recommendation summary."
        class="form-input"
    >{{ old('summary', $recommendation->summary) }}</textarea>
</div>

<div>
    <label class="form-label" for="reasoning">Reasoning</label>
    <textarea
        id="reasoning"
        name="reasoning"
        rows="8"
        placeholder="Detailed reasoning: bracket path, upset risk, team form, map pool, and Pick’em usage."
        class="form-input"
    >{{ old('reasoning', $recommendation->reasoning) }}</textarea>
</div>
