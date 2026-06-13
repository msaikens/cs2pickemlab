<div class="player-admin-form-grid">
    <div class="player-admin-field">
        <label class="form-label" for="handle">Handle</label>
        <input
            id="handle"
            name="handle"
            type="text"
            value="{{ old('handle', $player->handle) }}"
            placeholder="xertioN"
            class="form-input"
            required
        >

        @error('handle')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="slug">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $player->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >

        <p class="form-help">
            Leave blank to generate from the player handle.
        </p>

        @error('slug')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="team_id">Team</label>
        <select id="team_id" name="team_id" class="form-input">
            <option value="">No team / Free agent</option>

            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('team_id', $player->team_id) === (string) $team->id)>
                    {{ $team->name }}
                </option>
            @endforeach
        </select>

        @error('team_id')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="real_name">Real Name</label>
        <input
            id="real_name"
            name="real_name"
            type="text"
            value="{{ old('real_name', $player->real_name) }}"
            class="form-input"
        >

        @error('real_name')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="country">Country</label>
        <input
            id="country"
            name="country"
            type="text"
            value="{{ old('country', $player->country) }}"
            placeholder="Finland, Brazil, United States"
            class="form-input"
        >

        @error('country')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="role">Role</label>
        <select id="role" name="role" class="form-input">
            <option value="">Unknown / not set</option>

            @foreach([
                'awper' => 'AWPer',
                'rifler' => 'Rifler',
                'igl' => 'IGL',
                'support' => 'Support',
                'lurker' => 'Lurker',
                'entry' => 'Entry',
                'coach' => 'Coach',
                'substitute' => 'Substitute',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $player->role) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="rating">Rating</label>
        <input
            id="rating"
            name="rating"
            type="number"
            step="0.01"
            min="0"
            max="5"
            value="{{ old('rating', $player->rating) }}"
            placeholder="1.10"
            class="form-input"
        >

        @error('rating')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="kd_ratio">K/D Ratio</label>
        <input
            id="kd_ratio"
            name="kd_ratio"
            type="number"
            step="0.01"
            min="0"
            max="5"
            value="{{ old('kd_ratio', $player->kd_ratio) }}"
            placeholder="1.05"
            class="form-input"
        >

        @error('kd_ratio')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="impact_rating">Impact Rating</label>
        <input
            id="impact_rating"
            name="impact_rating"
            type="number"
            step="0.01"
            min="0"
            max="5"
            value="{{ old('impact_rating', $player->impact_rating) }}"
            placeholder="1.15"
            class="form-input"
        >

        @error('impact_rating')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'active' => 'Active',
                'benched' => 'Benched',
                'inactive' => 'Inactive',
                'retired' => 'Retired',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $player->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('status')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="player-admin-field player-admin-field-wide">
        <label class="form-label" for="photo_path">Photo Path</label>
        <input
            id="photo_path"
            name="photo_path"
            type="text"
            value="{{ old('photo_path', $player->photo_path) }}"
            placeholder="images/players/xertion.png"
            class="form-input"
        >

        @error('photo_path')
            <p class="player-admin-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="player-admin-field">
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing player summary."
        class="form-input"
    >{{ old('summary', $player->summary) }}</textarea>

    @error('summary')
        <p class="player-admin-error">{{ $message }}</p>
    @enderror
</div>

<div class="player-admin-field">
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="5"
        placeholder="Private notes on form, role, volatility, or roster context."
        class="form-input"
    >{{ old('notes', $player->notes) }}</textarea>

    @error('notes')
        <p class="player-admin-error">{{ $message }}</p>
    @enderror
</div>