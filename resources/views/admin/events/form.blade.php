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
    </div>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <label class="checkbox-card">
        <input
            type="checkbox"
            name="has_pickem"
            value="1"
            @checked(old('has_pickem', $event->has_pickem))
            class="checkbox-input"
        >
        <span>
            <span class="block font-bold text-white">Has Pick’em</span>
            <span class="block text-xs text-slate-500">Enable Pick’em recommendations for this event.</span>
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
            <span class="block font-bold text-white">Featured</span>
            <span class="block text-xs text-slate-500">Show this event prominently on public pages.</span>
        </span>
    </label>
</div>

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing event summary."
        class="form-input"
    >{{ old('summary', $event->summary) }}</textarea>
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
</div>
