<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('event_stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('team_one_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('team_two_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('winner_team_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->timestamp('starts_at')->nullable();

            $table->string('status')->default('scheduled');
            // scheduled, live, completed, postponed, cancelled

            $table->string('format')->default('bo3');
            // bo1, bo3, bo5

            $table->unsignedTinyInteger('team_one_score')->nullable();
            $table->unsignedTinyInteger('team_two_score')->nullable();

            $table->text('summary')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'starts_at']);
            $table->index(['event_id', 'event_stage_id']);
            $table->index(['team_one_id', 'team_two_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
