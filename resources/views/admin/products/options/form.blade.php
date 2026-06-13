<div class="product-admin-context-card">
    <p class="product-admin-eyebrow">Product</p>

    <p class="product-admin-context-title">
        {{ $product->name }}
    </p>
</div>

<div class="product-admin-form-grid">
    <div class="product-admin-field">
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

        @error('name')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="slug">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $option->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >

        <p class="form-help">
            Leave blank to generate from the option name.
        </p>

        @error('slug')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
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

        @error('type')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="sort_order">Sort Order</label>
        <input
            id="sort_order"
            name="sort_order"
            type="number"
            value="{{ old('sort_order', $option->sort_order ?? 0) }}"
            class="form-input"
        >

        @error('sort_order')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="product-admin-field">
    <label class="form-label" for="help_text">Help Text</label>
    <input
        id="help_text"
        name="help_text"
        type="text"
        value="{{ old('help_text', $option->help_text) }}"
        placeholder="Short instructions shown to the customer"
        class="form-input"
    >

    @error('help_text')
        <p class="product-admin-error">{{ $message }}</p>
    @enderror
</div>

<label class="product-admin-checkbox">
    <input
        type="checkbox"
        name="is_required"
        value="1"
        @checked(old('is_required', $option->is_required))
        class="checkbox-input"
    >

    <span>
        <span class="product-admin-checkbox-title">Required</span>
        <span class="product-admin-checkbox-help">
            Customer must complete this option before checkout.
        </span>
    </span>
</label>

@error('is_required')
    <p class="product-admin-error">{{ $message }}</p>
@enderror

<section class="product-admin-values-card">
    <div class="product-admin-values-header">
        <div>
            <h3>Selectable Values</h3>
            <p>
                Use these for select, radio, and checkbox options. Leave blank for text, textarea, file, number, or date options.
            </p>
        </div>
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

    <div class="product-admin-value-list">
        @foreach($existingValues as $index => $value)
            <div class="product-admin-value-row">
                <input type="hidden" name="values[{{ $index }}][id]" value="{{ $value['id'] ?? '' }}">

                <div class="product-admin-value-field product-admin-value-label">
                    <label class="form-label-sm">Label</label>
                    <input
                        name="values[{{ $index }}][label]"
                        type="text"
                        value="{{ $value['label'] ?? '' }}"
                        placeholder="Gold"
                        class="form-input-sm"
                    >
                </div>

                <div class="product-admin-value-field product-admin-value-value">
                    <label class="form-label-sm">Value</label>
                    <input
                        name="values[{{ $index }}][value]"
                        type="text"
                        value="{{ $value['value'] ?? '' }}"
                        placeholder="gold"
                        class="form-input-sm"
                    >
                </div>

                <div class="product-admin-value-field product-admin-value-price">
                    <label class="form-label-sm">Price +/-</label>
                    <input
                        name="values[{{ $index }}][price_delta_dollars]"
                        type="number"
                        step="0.01"
                        value="{{ $value['price_delta_dollars'] ?? '0.00' }}"
                        class="form-input-sm"
                    >
                </div>

                <div class="product-admin-value-field product-admin-value-sort">
                    <label class="form-label-sm">Sort</label>
                    <input
                        name="values[{{ $index }}][sort_order]"
                        type="number"
                        value="{{ $value['sort_order'] ?? ($index + 1) }}"
                        class="form-input-sm"
                    >
                </div>

                <div class="product-admin-value-field product-admin-value-delete">
                    @if(! empty($value['id']))
                        <label class="product-admin-delete-check">
                            <input type="checkbox" name="delete_values[]" value="{{ $value['id'] }}">
                            <span>Delete</span>
                        </label>
                    @else
                        <span class="product-admin-new-row">New row</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>