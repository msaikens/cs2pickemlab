<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_roster_players', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('event_stage_id')
                ->nullable()
                ->constrained('event_stages')
                ->nullOnDelete();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('player_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('role')->nullable(); // player, coach, substitute
            $table->boolean('is_starter')->default(true);
            $table->boolean('is_active')->default(true);

            $table->json('source_payload')->nullable();
            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'team_id', 'player_id']);
            $table->index(['event_id', 'event_stage_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_roster_players');
    }
};