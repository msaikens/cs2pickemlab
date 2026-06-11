<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steam_trade_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('steam_trade_url')->nullable();
            $table->string('trade_partner_id')->nullable();
            $table->string('trade_token')->nullable();

            $table->boolean('inventory_public')->default(false);
            $table->timestamp('last_inventory_sync_at')->nullable();
            $table->timestamp('trade_hold_warning_acknowledged_at')->nullable();

            $table->timestamps();

            $table->unique('user_id');
            $table->index('trade_partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steam_trade_profiles');
    }
};