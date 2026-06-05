<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoShopSeeder extends Seeder
{
    public function run(): void
    {
        $coin = Product::create([
            'name' => 'Custom Pick’em Champion Coin',
            'slug' => 'custom-pickem-champion-coin',
            'sku' => 'PLC-COIN-PICKEM',
            'short_description' => 'A custom gamer coin for Pick’em winners, Discord groups, and CS2 watch parties.',
            'description' => 'Create a custom Pick’em Champion Coin with gamer tag, event name, year, and finish options. Original gamer award design, not affiliated with Valve or Counter-Strike.',
            'base_price' => 1999,
            'status' => 'active',
            'product_type' => 'custom',
            'requires_customization' => true,
            'requires_upload' => false,
            'is_featured' => true,
            'sort_order' => 10,
            'primary_image_path' => null,
        ]);

        $this->addSelectOption($coin, 'Finish', 'finish', true, [
            ['Gold', 'gold', 300],
            ['Silver', 'silver', 200],
            ['Bronze', 'bronze', 100],
            ['Matte Black', 'matte-black', 400],
        ]);

        $this->addSelectOption($coin, 'Diameter', 'diameter', true, [
            ['50mm', '50mm', 0],
            ['60mm', '60mm', 500],
            ['75mm', '75mm', 900],
        ]);

        $this->addSelectOption($coin, 'Display Stand', 'display-stand', false, [
            ['No stand', 'no', 0],
            ['Add display stand', 'yes', 500],
        ]);

        $this->addTextOption($coin, 'Gamer Tag', 'gamer-tag', true, 'The name or handle to place on the coin.');
        $this->addTextOption($coin, 'Event Name', 'event-name', false, 'Example: Cologne Pick’em Group, Friday Night LAN, Discord Major Pool.');
        $this->addTextOption($coin, 'Year', 'year', false, 'Example: 2026.');

        ProductVariant::create([
            'product_id' => $coin->id,
            'sku' => 'PLC-COIN-PICKEM-BASE',
            'name' => 'Custom Pick’em Champion Coin',
            'price' => 1999,
            'inventory_quantity' => null,
            'is_active' => true,
        ]);

        $trophy = Product::create([
            'name' => 'LAN Champion Desk Trophy',
            'slug' => 'lan-champion-desk-trophy',
            'sku' => 'PLC-TROPHY-LAN',
            'short_description' => 'A custom desk trophy for LAN events, Discord tournaments, and squad competitions.',
            'description' => 'Custom winner trophy with team name, event name, placement, and optional logo upload. Original gamer award design.',
            'base_price' => 4999,
            'status' => 'active',
            'product_type' => 'custom',
            'requires_customization' => true,
            'requires_upload' => true,
            'is_featured' => true,
            'sort_order' => 20,
            'primary_image_path' => null,
        ]);

        $this->addSelectOption($trophy, 'Size', 'size', true, [
            ['Small', 'small', 0],
            ['Medium', 'medium', 1500],
            ['Large', 'large', 3000],
        ]);

        $this->addSelectOption($trophy, 'Finish', 'finish', true, [
            ['Black', 'black', 0],
            ['Gold', 'gold', 500],
            ['Silver', 'silver', 500],
        ]);

        $this->addTextOption($trophy, 'Team Name', 'team-name', true, 'The team or winner name.');
        $this->addTextOption($trophy, 'Event Name', 'event-name', true, 'The tournament, LAN, or Discord event name.');
        $this->addTextOption($trophy, 'Placement Text', 'placement-text', true, 'Example: Champions, 1st Place, MVP.');

        $logoUpload = ProductOption::create([
            'product_id' => $trophy->id,
            'name' => 'Logo Upload',
            'slug' => 'logo-upload',
            'type' => 'file',
            'is_required' => false,
            'sort_order' => 50,
            'help_text' => 'Upload a team logo or reference image. Original/owned artwork only.',
        ]);

        ProductVariant::create([
            'product_id' => $trophy->id,
            'sku' => 'PLC-TROPHY-LAN-BASE',
            'name' => 'LAN Champion Desk Trophy',
            'price' => 4999,
            'inventory_quantity' => null,
            'is_active' => true,
        ]);

        $pack = Product::create([
            'name' => 'Tournament Award Pack',
            'slug' => 'tournament-award-pack',
            'sku' => 'PLC-PACK-TOURNAMENT',
            'short_description' => 'A custom award bundle for small esports events, LAN parties, and Discord tournaments.',
            'description' => 'Bundle includes winner awards and optional MVP/top-fragger/support awards. Good for small CS2-style tournaments and gamer communities.',
            'base_price' => 12999,
            'status' => 'active',
            'product_type' => 'bundle',
            'requires_customization' => true,
            'requires_upload' => true,
            'is_featured' => true,
            'sort_order' => 30,
            'primary_image_path' => null,
        ]);

        $this->addSelectOption($pack, 'Pack Size', 'pack-size', true, [
            ['Starter Pack - 5 awards', 'starter-5', 0],
            ['Standard Pack - 10 awards', 'standard-10', 7000],
            ['Large Pack - 15 awards', 'large-15', 12000],
        ]);

        $this->addSelectOption($pack, 'Add MVP Award', 'add-mvp-award', false, [
            ['No', 'no', 0],
            ['Yes', 'yes', 1500],
        ]);

        $this->addSelectOption($pack, 'Add Top Fragger Award', 'add-top-fragger-award', false, [
            ['No', 'no', 0],
            ['Yes', 'yes', 1500],
        ]);

        $this->addTextOption($pack, 'Tournament Name', 'tournament-name', true, 'The tournament, LAN, or community event name.');
        $this->addTextOption($pack, 'Organizer Name', 'organizer-name', false, 'Optional organizer, server, or community name.');

        ProductOption::create([
            'product_id' => $pack->id,
            'name' => 'Logo Upload',
            'slug' => 'logo-upload',
            'type' => 'file',
            'is_required' => false,
            'sort_order' => 50,
            'help_text' => 'Upload a community, team, or tournament logo. Original/owned artwork only.',
        ]);

        ProductVariant::create([
            'product_id' => $pack->id,
            'sku' => 'PLC-PACK-TOURNAMENT-BASE',
            'name' => 'Tournament Award Pack',
            'price' => 12999,
            'inventory_quantity' => null,
            'is_active' => true,
        ]);
    }

    private function addSelectOption(Product $product, string $name, string $slug, bool $required, array $values): ProductOption
    {
        $option = ProductOption::create([
            'product_id' => $product->id,
            'name' => $name,
            'slug' => $slug,
            'type' => 'select',
            'is_required' => $required,
            'sort_order' => ProductOption::where('product_id', $product->id)->count() + 1,
            'help_text' => null,
        ]);

        foreach ($values as $index => [$label, $value, $priceDelta]) {
            ProductOptionValue::create([
                'product_option_id' => $option->id,
                'label' => $label,
                'value' => $value,
                'price_delta' => $priceDelta,
                'sort_order' => $index + 1,
            ]);
        }

        return $option;
    }

    private function addTextOption(Product $product, string $name, string $slug, bool $required, ?string $helpText = null): ProductOption
    {
        return ProductOption::create([
            'product_id' => $product->id,
            'name' => $name,
            'slug' => $slug,
            'type' => 'text',
            'is_required' => $required,
            'sort_order' => ProductOption::where('product_id', $product->id)->count() + 1,
            'help_text' => $helpText,
        ]);
    }
}
