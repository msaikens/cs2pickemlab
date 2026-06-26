<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// app/Database/Migrations/2026_06_05_220958_create_order_item_customizations_table.php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_customizations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_option_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('label');
            $table->longText('value')->nullable();

            $table->integer('price_delta')->default(0); // cents

            $table->timestamps();

            $table->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_customizations');
    }
};
