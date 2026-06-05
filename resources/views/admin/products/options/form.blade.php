<div class="card-dark">
    <p class="page-eyebrow">Product</p>
    <p class="mt-1 text-xl font-black text-white">{{ $product->name }}</p>
</div>

<div class="form-grid">
    <div>
        <label class="form-label" for="name">Option Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $option->name) }}"
            placeholder="Finish, Size, Gamer Tag, Logo Upload"
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
            value="{{ old('slug', $option->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >
    </div>

    <div>
        <label class="form-label" for="type">Type</label>
        <select id="type" name="type" class="form-input" required>
            @foreach([
                'select' => 'Select dropdown',
                'radio' => 'Radio buttons',
                'checkbox' => 'Checkbox',
                'text' => 'Text',
                'textarea' => 'Textarea',
                'file' => 'File upload',
                'number' => 'Number',
                'date' => 'Date',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $option->type) === $value)>
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
            value="{{ old('sort_order', $option->sort_order ?? 0) }}"
            class="form-input"
        >
    </div>
</div>

<div>
    <label class="form-label" for="help_text">Help Text</label>
    <input
        id="help_text"
        name="help_text"
        type="text"
        value="{{ old('help_text', $option->help_text) }}"
        placeholder="Short instructions shown to the customer"
        class="form-input"
    >
</div>

<label class="checkbox-card">
    <input
        type="checkbox"
        name="is_required"
        value="1"
        @checked(old('is_required', $option->is_required))
        class="checkbox-input"
    >
    <span>
        <span class="block font-bold text-white">Required</span>
        <span class="block text-xs text-slate-500">Customer must complete this option before checkout.</span>
    </span>
</label>

<div class="card-dark">
    <div class="mb-4">
        <h3 class="text-xl font-black text-white">Selectable Values</h3>
        <p class="mt-1 text-sm text-slate-400">
            Use these for select, radio, and checkbox options. Leave blank for text, textarea, file, number, or date options.
        </p>
    </div>

    @php
        $existingValues = old('values');

        if ($existingValues === null) {
            $existingValues = $option->exists
                ? $option->values->map(fn ($value) => [
                    'id' => $value->id,
                    'label' => $value->label,
                    'value' => $value->value,
                    'price_delta_dollars' => number_format($value->price_delta / 100, 2, '.', ''),
                    'sort_order' => $value->sort_order,
                ])->toArray()
                : [];
        }

        $blankRowsNeeded = max(3, 6 - count($existingValues));

        for ($i = 0; $i < $blankRowsNeeded; $i++) {
            $existingValues[] = [
                'id' => null,
                'label' => '',
                'value' => '',
                'price_delta_dollars' => '0.00',
                'sort_order' => count($existingValues) + 1,
            ];
        }
    @endphp

    <div class="space-y-3">
        @foreach($existingValues as $index => $value)
            <div class="grid gap-3 rounded-lg border border-slate-800 bg-slate-900 p-4 lg:grid-cols-12">
                <input type="hidden" name="values[{{ $index }}][id]" value="{{ $value['id'] ?? '' }}">

                <div class="lg:col-span-3">
                    <label class="form-label-sm">Label</label>
                    <input
                        name="values[{{ $index }}][label]"
                        type="text"
                        value="{{ $value['label'] ?? '' }}"
                        placeholder="Gold"
                        class="form-input-sm"
                    >
                </div>

                <div class="lg:col-span-3">
                    <label class="form-label-sm">Value</label>
                    <input
                        name="values[{{ $index }}][value]"
                        type="text"
                        value="{{ $value['value'] ?? '' }}"
                        placeholder="gold"
                        class="form-input-sm"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label class="form-label-sm">Price +/-</label>
                    <input
                        name="values[{{ $index }}][price_delta_dollars]"
                        type="number"
                        step="0.01"
                        value="{{ $value['price_delta_dollars'] ?? '0.00' }}"
                        class="form-input-sm"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label class="form-label-sm">Sort</label>
                    <input
                        name="values[{{ $index }}][sort_order]"
                        type="number"
                        value="{{ $value['sort_order'] ?? ($index + 1) }}"
                        class="form-input-sm"
                    >
                </div>

                <div class="flex items-end lg:col-span-2">
                    @if(! empty($value['id']))
                        <label class="flex items-center gap-2 text-sm text-red-300">
                            <input type="checkbox" name="delete_values[]" value="{{ $value['id'] }}">
                            Delete
                        </label>
                    @else
                        <span class="pb-2 text-xs text-slate-600">New row</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
