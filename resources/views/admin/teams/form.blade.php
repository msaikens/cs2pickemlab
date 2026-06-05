<div class="form-grid">
    <div>
        <label class="form-label" for="name">Team Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $team->name) }}"
            placeholder="MOUZ"
            class="form-input"
            required
        >
    </div>

    <div>
        <label class="form-label" for="slug">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $team->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="short_name">Short Name</label>
        <input
            id="short_name"
            name="short_name"
            type="text"
            value="{{ old('short_name', $team->short_name) }}"
            placeholder="MOUZ"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="picklab_rating">PickLab Rating</label>
        <input
            id="picklab_rating"
            name="picklab_rating"
            type="number"
            min="0"
            max="3000"
            value="{{ old('picklab_rating', $team->picklab_rating ?? 1500) }}"
            class="form-input"
            required
        >
        <p class="form-help">Internal rating used for prediction confidence. Default: 1500.</p>
    </div>

    <div>
        <label class="form-label" for="region">Region</label>
        <input
            id="region"
            name="region"
            type="text"
            value="{{ old('region', $team->region) }}"
            placeholder="Europe, North America, South America"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="country">Country</label>
        <input
            id="country"
            name="country"
            type="text"
            value="{{ old('country', $team->country) }}"
            placeholder="International, United States, Brazil"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $team->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="logo_path">Logo Path</label>
        <input
            id="logo_path"
            name="logo_path"
            type="text"
            value="{{ old('logo_path', $team->logo_path) }}"
            placeholder="images/teams/mouz.png"
            class="form-input"
        >
    </div>
</div>

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing team profile."
        class="form-input"
    >{{ old('summary', $team->summary) }}</textarea>
</div>

<div>
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="5"
        placeholder="Private admin notes about form, roster stability, map pool, or risk."
        class="form-input"
    >{{ old('notes', $team->notes) }}</textarea>
</div>
