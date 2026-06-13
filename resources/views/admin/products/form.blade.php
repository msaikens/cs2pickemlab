<div class="product-admin-form-grid">
    <div class="product-admin-field">
        <label class="form-label" for="name">Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $product->name) }}"
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
            value="{{ old('slug', $product->slug) }}"
            placeholder="auto-generated if blank"
            class="form-input"
        >

        <p class="form-help">
            Leave blank to generate from the product name.
        </p>

        @error('slug')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="sku">SKU</label>
        <input
            id="sku"
            name="sku"
            type="text"
            value="{{ old('sku', $product->sku) }}"
            class="form-input"
        >

        @error('sku')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="base_price_dollars">Base Price</label>
        <input
            id="base_price_dollars"
            name="base_price_dollars"
            type="number"
            step="0.01"
            min="0"
            value="{{ old('base_price_dollars', number_format(($product->base_price ?? 0) / 100, 2, '.', '')) }}"
            class="form-input"
            required
        >

        <p class="form-help">
            Enter dollars. Example: 19.99
        </p>

        @error('base_price_dollars')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-input" required>
            @foreach([
                'draft' => 'Draft',
                'active' => 'Active',
                'archived' => 'Archived',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $product->status) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('status')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="product_type">Product Type</label>
        <select id="product_type" name="product_type" class="form-input" required>
            @foreach([
                'physical' => 'Physical',
                'digital' => 'Digital',
                'service' => 'Service',
                'bundle' => 'Bundle',
                'custom' => 'Custom',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('product_type', $product->product_type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('product_type')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="sort_order">Sort Order</label>
        <input
            id="sort_order"
            name="sort_order"
            type="number"
            value="{{ old('sort_order', $product->sort_order ?? 0) }}"
            class="form-input"
        >

        @error('sort_order')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="primary_image_path">Primary Image Path</label>
        <input
            id="primary_image_path"
            name="primary_image_path"
            type="text"
            value="{{ old('primary_image_path', $product->primary_image_path) }}"
            placeholder="images/products/example.png"
            class="form-input"
        >

        @error('primary_image_path')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="product-admin-field">
    <label class="form-label" for="short_description">Short Description</label>
    <input
        id="short_description"
        name="short_description"
        type="text"
        value="{{ old('short_description', $product->short_description) }}"
        class="form-input"
    >

    @error('short_description')
        <p class="product-admin-error">{{ $message }}</p>
    @enderror
</div>

<div class="product-admin-field">
    <label class="form-label" for="description">Description</label>
    <textarea
        id="description"
        name="description"
        rows="6"
        class="form-input product-admin-description"
    >{{ old('description', $product->description) }}</textarea>

    @error('description')
        <p class="product-admin-error">{{ $message }}</p>
    @enderror
</div>

<div class="product-admin-checkbox-grid">
    <label class="product-admin-checkbox">
        <input
            type="checkbox"
            name="requires_customization"
            value="1"
            @checked(old('requires_customization', $product->requires_customization))
            class="checkbox-input"
        >

        <span>
            <span class="product-admin-checkbox-title">Requires customization</span>
            <span class="product-admin-checkbox-help">
                Customer must provide text/options.
            </span>
        </span>
    </label>

    <label class="product-admin-checkbox">
        <input
            type="checkbox"
            name="requires_upload"
            value="1"
            @checked(old('requires_upload', $product->requires_upload))
            class="checkbox-input"
        >

        <span>
            <span class="product-admin-checkbox-title">Requires upload</span>
            <span class="product-admin-checkbox-help">
                Customer may upload logo/reference.
            </span>
        </span>
    </label>

    <label class="product-admin-checkbox">
        <input
            type="checkbox"
            name="is_featured"
            value="1"
            @checked(old('is_featured', $product->is_featured))
            class="checkbox-input"
        >

        <span>
            <span class="product-admin-checkbox-title">Featured</span>
            <span class="product-admin-checkbox-help">
                Show on homepage/shop top.
            </span>
        </span>
    </label>
</div>

@error('requires_customization')
    <p class="product-admin-error">{{ $message }}</p>
@enderror

@error('requires_upload')
    <p class="product-admin-error">{{ $message }}</p>
@enderror

@error('is_featured')
    <p class="product-admin-error">{{ $message }}</p>
@enderror