<div class="card-dark">
    <p class="page-eyebrow">Product</p>
    <p class="mt-1 text-xl font-black text-white">{{ $product->name }}</p>
    <p class="mt-1 text-muted-sm">Base product price: ${{ $product->base_price_dollars }}</p>
</div>

<div class="form-grid">
    <div>
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
    </div>

    <div>
        <label class="form-label" for="sku">SKU</label>
        <input
            id="sku"
            name="sku"
            type="text"
            value="{{ old('sku', $variant->sku) }}"
            placeholder="PLC-COIN-BASE"
            class="form-input"
        >
    </div>

    <div>
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
        <p class="form-help">Enter dollars. Example: 24.99</p>
    </div>

    <div>
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
        <p class="form-help">Leave blank if this is made-to-order or unlimited.</p>
    </div>
</div>

<label class="checkbox-card">
    <input
        type="checkbox"
        name="is_active"
        value="1"
        @checked(old('is_active', $variant->is_active ?? true))
        class="checkbox-input"
    >
    <span>
        <span class="block font-bold text-white">Active</span>
        <span class="block text-xs text-slate-500">Inactive variants will not be offered to customers.</span>
    </span>
</label>
