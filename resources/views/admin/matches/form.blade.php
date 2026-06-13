<div class="match-admin-form-grid">
    <div class="match-admin-field">
        <label class="form-label" for="event_id">Event</label>
        <select id="event_id" name="event_id" class="form-input">
            <option value="">No event</option>

            @foreach($events as $event)
                <option value="{{ $event->id }}" @selected((string) old('event_id', $match->event_id) === (string) $event->id)>
                    {{ $event->name }}
                </option>
            @endforeach
        </select>

        @error('event_id')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="event_stage_id">Stage</label>
        <select id="event_stage_id" name="event_stage_id" class="form-input">
            <option value="">No stage</option>

            @foreach($stages as $stage)
                <option value="{{ $stage->id }}" @selected((string) old('event_stage_id', $match->event_stage_id) === (string) $stage->id)>
                    {{ $stage->event?->name }} — {{ $stage->name }}
                </option>
            @endforeach
        </select>

        <p class="form-help">
            Pick a stage from the selected event. We’ll make this dynamic later.
        </p>

        @error('event_stage_id')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="team_one_id">Team One</label>
        <select id="team_one_id" name="team_one_id" class="form-input" required>
            <option value="">Choose team</option>

            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('team_one_id', $match->team_one_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>

        @error('team_one_id')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="team_two_id">Team Two</label>
        <select id="team_two_id" name="team_two_id" class="form-input" required>
            <option value="">Choose team</option>

            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('team_two_id', $match->team_two_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>

        @error('team_two_id')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="starts_at">Start Time</label>
        <input
            id="starts_at"
            name="starts_at"
            type="datetime-local"
            value="{{ old('starts_at', $match->starts_at?->format('Y-m-d\TH:i')) }}"
            class="form-input"
        >

        @error('starts_at')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="format">Format</label>
        <select id="format" name="format" class="form-input" required>
            @foreach([
                'bo1' => 'BO1',
                'bo3' => 'BO3',
                'bo5' => 'BO5',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('format', $match->format) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('format')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'scheduled' => 'Scheduled',
                'live' => 'Live',
                'completed' => 'Completed',
                'postponed' => 'Postponed',
                'cancelled' => 'Cancelled',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $match->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('status')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="winner_team_id">Winner</label>
        <select id="winner_team_id" name="winner_team_id" class="form-input">
            <option value="">TBD / no winner</option>

            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('winner_team_id', $match->winner_team_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>

        <p class="form-help">Only set after match completion.</p>

        @error('winner_team_id')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="bracket_group">Bracket Group</label>
        <select id="bracket_group" name="bracket_group" class="form-input">
            <option value="">None / General Match</option>
            <option value="swiss" @selected(old('bracket_group', $match->bracket_group) === 'swiss')>
                Swiss
            </option>
            <option value="playoffs" @selected(old('bracket_group', $match->bracket_group) === 'playoffs')>
                Playoffs
            </option>
        </select>

        <p class="form-help">Use Playoffs for standard bracket display.</p>

        @error('bracket_group')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="round_label">Round Label</label>
        <select id="round_label" name="round_label" class="form-input">
            <option value="">None</option>

            @foreach([
                'Swiss Round 1',
                'Swiss Round 2',
                'Swiss Round 3',
                'Swiss Round 4',
                'Swiss Round 5',
                'Quarterfinals',
                'Semifinals',
                'Grand Final',
            ] as $round)
                <option value="{{ $round }}" @selected(old('round_label', $match->round_label) === $round)>
                    {{ $round }}
                </option>
            @endforeach
        </select>

        <p class="form-help">
            For bracket display, use Quarterfinals, Semifinals, or Grand Final.
        </p>

        @error('round_label')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="bracket_position">Bracket Position</label>
        <input
            id="bracket_position"
            name="bracket_position"
            type="number"
            min="0"
            value="{{ old('bracket_position', $match->bracket_position ?? 0) }}"
            class="form-input"
        >

        <p class="form-help">
            Quarterfinals: 1-4. Semifinals: 1-2. Grand Final: 1.
        </p>

        @error('bracket_position')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="team_one_score">Team One Score</label>
        <input
            id="team_one_score"
            name="team_one_score"
            type="number"
            min="0"
            max="5"
            value="{{ old('team_one_score', $match->team_one_score) }}"
            class="form-input"
        >

        @error('team_one_score')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="match-admin-field">
        <label class="form-label" for="team_two_score">Team Two Score</label>
        <input
            id="team_two_score"
            name="team_two_score"
            type="number"
            min="0"
            max="5"
            value="{{ old('team_two_score', $match->team_two_score) }}"
            class="form-input"
        >

        @error('team_two_score')
            <p class="match-admin-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="match-admin-field">
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing match context."
        class="form-input"
    >{{ old('summary', $match->summary) }}</textarea>

    @error('summary')
        <p class="match-admin-error">{{ $message }}</p>
    @enderror
</div>

<div class="match-admin-field">
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="5"
        placeholder="Private notes about veto, map risk, roster context, or prediction concerns."
        class="form-input"
    >{{ old('notes', $match->notes) }}</textarea>

    @error('notes')
        <p class="match-admin-error">{{ $message }}</p>
    @enderror
</div>