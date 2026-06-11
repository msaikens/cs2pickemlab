<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skin_listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('steam_asset_id')->nullable();
            $table->string('market_hash_name');
            $table->string('item_name');
            $table->string('weapon_type')->nullable();
            $table->string('rarity')->nullable();
            $table->string('wear_name')->nullable();
            $table->decimal('float_value', 10, 8)->nullable();
            $table->text('image_url')->nullable();

            $table->string('listing_type')->default('trade');
            $table->unsignedInteger('asking_price_cents')->nullable();
            $table->string('currency', 3)->default('USD');

            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('user_id');
            $table->index('steam_asset_id');
            $table->index('market_hash_name');
            $table->index('status');
            $table->index('listing_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skin_listings');
    }
};