<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('display_name')->nullable();
            $table->text('about')->nullable();

            $table->string('steam_name')->nullable();
            $table->string('steam_id')->nullable();
            $table->string('faceit_name')->nullable();
            $table->string('discord_name')->nullable();
            $table->string('twitch_name')->nullable();
            $table->string('youtube_name')->nullable();

            $table->string('country')->nullable();
            $table->string('timezone')->nullable();

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
