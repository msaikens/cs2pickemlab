<div class="product-admin-context-card">
    <p class="product-admin-eyebrow">Product</p>

    <p class="product-admin-context-title">
        {{ $product->name }}
    </p>

    <p class="product-admin-context-subtitle">
        Base product price: ${{ $product->base_price_dollars }}
    </p>
</div>

<div class="product-admin-form-grid">
    <div class="product-admin-field">
        <label class="form-label" for="name">Variant Name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $variant->name) }}"
            placeholder="Custom Coin - Base, Trophy - Large"
            class="form-input"
            required
        >

        @error('name')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="sku">SKU</label>
        <input
            id="sku"
            name="sku"
            type="text"
            value="{{ old('sku', $variant->sku) }}"
            placeholder="PLC-COIN-BASE"
            class="form-input"
        >

        @error('sku')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="price_dollars">Variant Price</label>
        <input
            id="price_dollars"
            name="price_dollars"
            type="number"
            step="0.01"
            min="0"
            value="{{ old('price_dollars', number_format(($variant->price ?? 0) / 100, 2, '.', '')) }}"
            class="form-input"
            required
        >

        <p class="form-help">
            Enter dollars. Example: 24.99
        </p>

        @error('price_dollars')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="product-admin-field">
        <label class="form-label" for="inventory_quantity">Inventory Quantity</label>
        <input
            id="inventory_quantity"
            name="inventory_quantity"
            type="number"
            min="0"
            value="{{ old('inventory_quantity', $variant->inventory_quantity) }}"
            placeholder="Leave blank for made-to-order"
            class="form-input"
        >

        <p class="form-help">
            Leave blank if this is made-to-order or unlimited.
        </p>

        @error('inventory_quantity')
            <p class="product-admin-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<label class="product-admin-checkbox">
    <input
        type="checkbox"
        name="is_active"
        value="1"
        @checked(old('is_active', $variant->is_active ?? true))
        class="checkbox-input"
    >

    <span>
        <span class="product-admin-checkbox-title">Active</span>
        <span class="product-admin-checkbox-help">
            Inactive variants will not be offered to customers.
        </span>
    </span>
</label>

@error('is_active')
    <p class="product-admin-error">{{ $message }}</p>
@enderror