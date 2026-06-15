<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_stat_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('event_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('event_stage_id')
                ->nullable()
                ->constrained('event_stages')
                ->nullOnDelete();

            $table->string('source')->default('manual');
            $table->string('scope')->default('recent');
            $table->date('snapshot_date');

            $table->unsignedInteger('matches_played')->nullable();
            $table->unsignedInteger('maps_played')->nullable();

            $table->decimal('match_win_rate', 6, 2)->nullable();
            $table->decimal('map_win_rate', 6, 2)->nullable();
            $table->decimal('round_win_rate', 6, 2)->nullable();
            $table->decimal('ct_round_win_rate', 6, 2)->nullable();
            $table->decimal('t_round_win_rate', 6, 2)->nullable();
            $table->decimal('pistol_win_rate', 6, 2)->nullable();

            $table->decimal('average_player_rating', 5, 2)->nullable();
            $table->decimal('average_adr', 6, 2)->nullable();
            $table->decimal('form_score', 6, 2)->nullable();

            $table->json('map_pool')->nullable();
            $table->json('source_payload')->nullable();

            $table->timestamps();

            $table->index(['team_id', 'event_id', 'scope']);
            $table->index(['event_id', 'event_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_stat_snapshots');
    }
};