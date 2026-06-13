<div class="admin-form-grid two">
    <div class="admin-field">
        <label for="name">Team Name</label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $team->name) }}"
            placeholder="MOUZ"
            required
        >
    </div>

    <div class="admin-field">
        <label for="slug">Slug</label>

        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $team->slug) }}"
            placeholder="auto-generated if blank"
        >
    </div>

    <div class="admin-field">
        <label for="short_name">Short Name</label>

        <input
            id="short_name"
            name="short_name"
            type="text"
            value="{{ old('short_name', $team->short_name) }}"
            placeholder="MOUZ"
        >
    </div>

    <div class="admin-field">
        <label for="picklab_rating">PickLab Rating</label>

        <input
            id="picklab_rating"
            name="picklab_rating"
            type="number"
            min="0"
            max="3000"
            value="{{ old('picklab_rating', $team->picklab_rating ?? 1500) }}"
            required
        >

        <p class="admin-field-help">
            Internal rating used for prediction confidence. Default: 1500.
        </p>
    </div>

    <div class="admin-field">
        <label for="region">Region</label>

        <input
            id="region"
            name="region"
            type="text"
            value="{{ old('region', $team->region) }}"
            placeholder="Europe, North America, South America"
        >
    </div>

    <div class="admin-field">
        <label for="country">Country</label>

        <input
            id="country"
            name="country"
            type="text"
            value="{{ old('country', $team->country) }}"
            placeholder="International, United States, Brazil"
        >
    </div>

    <div class="admin-field">
        <label for="status">Status</label>

        <select id="status" name="status" required>
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

    <div class="admin-field">
        <label for="logo_path">Logo Path</label>

        <input
            id="logo_path"
            name="logo_path"
            type="text"
            value="{{ old('logo_path', $team->logo_path) }}"
            placeholder="images/teams/mouz.png"
        >
    </div>

    <div class="admin-field full">
        <label for="summary">Summary</label>

        <textarea
            id="summary"
            name="summary"
            rows="4"
            placeholder="Short public-facing team profile."
        >{{ old('summary', $team->summary) }}</textarea>
    </div>

    <div class="admin-field full">
        <label for="notes">Internal Notes</label>

        <textarea
            id="notes"
            name="notes"
            rows="5"
            placeholder="Private admin notes about form, roster stability, map pool, or risk."
        >{{ old('notes', $team->notes) }}</textarea>
    </div>
</div>