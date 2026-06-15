<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_tournament_cache', function (Blueprint $table) {
            $table->id();

            $table->string('grid_tournament_id')->unique();
            $table->string('name');

            $table->string('grid_title_id')->nullable();
            $table->boolean('is_cs2')->nullable();

            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('grid_title_id');
            $table->index('is_cs2');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_tournament_cache');
    }
}; 