<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_option_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('label');
            $table->string('value');

            $table->integer('price_delta')->default(0); // cents, can be negative if needed
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['product_option_id', 'value']);
            $table->index(['product_option_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
