<div class="form-grid">
    <div>
        <label class="form-label" for="match_id">Match</label>
        <select id="match_id" name="match_id" class="form-input" required>
            <option value="">Choose match</option>

            @foreach($matches as $match)
                @php
                    $hasOtherPrediction = $match->prediction && (int) $match->prediction->id !== (int) $prediction->id;
                @endphp

                <option
                    value="{{ $match->id }}"
                    @selected((string) old('match_id', $prediction->match_id) === (string) $match->id)
                    @disabled($hasOtherPrediction)
                >
                    {{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}
                    @if($match->event)
                        · {{ $match->event->name }}
                    @endif
                    @if($hasOtherPrediction)
                        · already has prediction
                    @endif
                </option>
            @endforeach
        </select>
        <p class="form-help">Each match can have one prediction.</p>
    </div>

    <div>
        <label class="form-label" for="predicted_winner_team_id">Predicted Winner</label>
        <select id="predicted_winner_team_id" name="predicted_winner_team_id" class="form-input">
            <option value="">TBD / no winner selected</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('predicted_winner_team_id', $prediction->predicted_winner_team_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>
        <p class="form-help">For now this lists all teams. Later we can limit it to the selected match teams.</p>
    </div>

    <div>
        <label class="form-label" for="confidence_score">Confidence Score</label>
        <input
            id="confidence_score"
            name="confidence_score"
            type="number"
            min="0"
            max="100"
            value="{{ old('confidence_score', $prediction->confidence_score ?? 50) }}"
            class="form-input"
            required
        >
    </div>

    <div>
        <label class="form-label" for="upset_risk">Upset Risk</label>
        <select id="upset_risk" name="upset_risk" class="form-input" required>
            @foreach([
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('upset_risk', $prediction->upset_risk) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="best_pickem_use">Best Pick’em Use</label>
        <select id="best_pickem_use" name="best_pickem_use" class="form-input">
            <option value="">Not set</option>
            @foreach([
                'safe_3_0' => 'Safe 3:0',
                'risky_3_0' => 'Risky 3:0',
                'safe_advancement' => 'Safe Advancement',
                'risky_advancement' => 'Risky Advancement',
                'avoid' => 'Avoid',
                'upset_watch' => 'Upset Watch',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('best_pickem_use', $prediction->best_pickem_use) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $prediction->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="published_at">Published At</label>
        <input
            id="published_at"
            name="published_at"
            type="datetime-local"
            value="{{ old('published_at', $prediction->published_at?->format('Y-m-d\TH:i')) }}"
            class="form-input"
        >
    </div>
</div>

<label class="checkbox-card">
    <input
        type="checkbox"
        name="is_premium"
        value="1"
        @checked(old('is_premium', $prediction->is_premium))
        class="checkbox-input"
    >
    <span>
        <span class="block font-bold text-white">Premium Prediction</span>
        <span class="block text-xs text-slate-500">Hide full reasoning later behind subscription access.</span>
    </span>
</label>

<div>
    <label class="form-label" for="headline">Headline</label>
    <input
        id="headline"
        name="headline"
        type="text"
        value="{{ old('headline', $prediction->headline) }}"
        placeholder="MOUZ are safer, but not a free 3:0"
        class="form-input"
    >
</div>

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public prediction summary."
        class="form-input"
    >{{ old('summary', $prediction->summary) }}</textarea>
</div>

<div>
    <label class="form-label" for="reasoning">Reasoning</label>
    <textarea
        id="reasoning"
        name="reasoning"
        rows="8"
        placeholder="Detailed reasoning: form, map pool, event context, upset risk, and Pick’em implications."
        class="form-input"
    >{{ old('reasoning', $prediction->reasoning) }}</textarea>
</div>
