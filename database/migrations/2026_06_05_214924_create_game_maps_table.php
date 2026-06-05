<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_maps', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug', 191)->unique();

            $table->string('status')->default('active');
            // active, inactive, removed

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_maps');
    }
};
