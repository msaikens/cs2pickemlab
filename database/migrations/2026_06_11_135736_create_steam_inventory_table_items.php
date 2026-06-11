<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steam_inventory_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('steam_id_64');
            $table->string('asset_id');
            $table->string('class_id')->nullable();
            $table->string('instance_id')->nullable();

            $table->string('app_id')->default('730');
            $table->string('context_id')->default('2');

            $table->string('market_hash_name');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('rarity')->nullable();
            $table->string('exterior')->nullable();

            $table->text('icon_url')->nullable();
            $table->text('image_url')->nullable();

            $table->boolean('tradable')->default(false);
            $table->boolean('marketable')->default(false);
            $table->boolean('commodity')->default(false);

            $table->json('raw_asset')->nullable();
            $table->json('raw_description')->nullable();

            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'asset_id']);
            $table->index('steam_id_64');
            $table->index('market_hash_name');
            $table->index('tradable');
            $table->index('marketable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steam_inventory_items');
    }
};