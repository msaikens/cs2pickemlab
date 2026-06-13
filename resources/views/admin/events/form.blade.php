<div class="form-grid">
    <div>
        <label class="form-label" for="name">Event Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $event->name) }}"
            placeholder="BLAST.tv Austin Major 2025"
            class="form-input"
            required
        >

        @error('name')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="slug">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $event->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >

        <p class="form-help">
            Leave blank to generate from the event name.
        </p>

        @error('slug')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="organizer">Organizer</label>
        <input
            id="organizer"
            name="organizer"
            type="text"
            value="{{ old('organizer', $event->organizer) }}"
            placeholder="BLAST, ESL, PGL, PickLab"
            class="form-input"
        >

        @error('organizer')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="location">Location</label>
        <input
            id="location"
            name="location"
            type="text"
            value="{{ old('location', $event->location) }}"
            placeholder="Austin, Cologne, Online"
            class="form-input"
        >

        @error('location')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="starts_on">Start Date</label>
        <input
            id="starts_on"
            name="starts_on"
            type="date"
            value="{{ old('starts_on', $event->starts_on?->format('Y-m-d')) }}"
            class="form-input"
        >

        @error('starts_on')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="ends_on">End Date</label>
        <input
            id="ends_on"
            name="ends_on"
            type="date"
            value="{{ old('ends_on', $event->ends_on?->format('Y-m-d')) }}"
            class="form-input"
        >

        @error('ends_on')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'upcoming' => 'Upcoming',
                'live' => 'Live',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $event->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('status')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="checkbox-grid">
    <label class="checkbox-card">
        <input
            type="checkbox"
            name="has_pickem"
            value="1"
            @checked(old('has_pickem', $event->has_pickem))
            class="checkbox-input"
        >

        <span>
            <span class="checkbox-title">Has Pick’em</span>
            <span class="checkbox-help">
                Enable Pick’em recommendations for this event.
            </span>
        </span>
    </label>

    <label class="checkbox-card">
        <input
            type="checkbox"
            name="is_featured"
            value="1"
            @checked(old('is_featured', $event->is_featured))
            class="checkbox-input"
        >

        <span>
            <span class="checkbox-title">Featured</span>
            <span class="checkbox-help">
                Show this event prominently on public pages.
            </span>
        </span>
    </label>
</div>

@error('has_pickem')
    <p class="form-error">{{ $message }}</p>
@enderror

@error('is_featured')
    <p class="form-error">{{ $message }}</p>
@enderror

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing event summary."
        class="form-input"
    >{{ old('summary', $event->summary) }}</textarea>

    @error('summary')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="5"
        placeholder="Private notes about format, stage rules, seeding, or Pick’em context."
        class="form-input"
    >{{ old('notes', $event->notes) }}</textarea>

    @error('notes')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>