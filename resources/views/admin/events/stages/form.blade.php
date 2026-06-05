<div class="card-dark">
    <p class="page-eyebrow">Event</p>
    <p class="mt-1 text-xl font-black text-white">{{ $event->name }}</p>
</div>

<div class="form-grid">
    <div>
        <label class="form-label" for="name">Stage Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $stage->name) }}"
            placeholder="Stage 1, Stage 2, Playoffs"
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
            value="{{ old('slug', $stage->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="starts_on">Start Date</label>
        <input
            id="starts_on"
            name="starts_on"
            type="date"
            value="{{ old('starts_on', $stage->starts_on?->format('Y-m-d')) }}"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="ends_on">End Date</label>
        <input
            id="ends_on"
            name="ends_on"
            type="date"
            value="{{ old('ends_on', $stage->ends_on?->format('Y-m-d')) }}"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="format">Format</label>
        <select id="format" name="format" class="form-input">
            <option value="">Not set</option>
            @foreach([
                'swiss' => 'Swiss',
                'groups' => 'Groups',
                'playoffs' => 'Playoffs',
                'single_elim' => 'Single Elimination',
                'double_elim' => 'Double Elimination',
                'round_robin' => 'Round Robin',
                'other' => 'Other',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('format', $stage->format) === $value)>
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
            value="{{ old('sort_order', $stage->sort_order ?? 0) }}"
            class="form-input"
        >
    </div>
</div>

<label class="checkbox-card">
    <input
        type="checkbox"
        name="has_pickem"
        value="1"
        @checked(old('has_pickem', $stage->has_pickem))
        class="checkbox-input"
    >
    <span>
        <span class="block font-bold text-white">Has Pick’em</span>
        <span class="block text-xs text-slate-500">Enable Pick’em recommendations for this stage.</span>
    </span>
</label>

<div>
    <label class="form-label" for="summary">Summary</label>
    <textarea
        id="summary"
        name="summary"
        rows="4"
        placeholder="Short public-facing stage summary."
        class="form-input"
    >{{ old('summary', $stage->summary) }}</textarea>
</div>

<div>
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="5"
        placeholder="Private notes about stage structure, Pick’em slots, seeding, or format."
        class="form-input"
    >{{ old('notes', $stage->notes) }}</textarea>
</div>
