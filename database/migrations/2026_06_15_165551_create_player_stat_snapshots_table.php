<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_stat_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('player_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('event_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('event_stage_id')
                ->nullable()
                ->constrained('event_stages')
                ->nullOnDelete();

            $table->string('source')->default('manual'); // grid, hltv, manual, import_csv
            $table->string('scope')->default('recent'); // recent, event, stage, last_3_months, all_time
            $table->date('snapshot_date');

            $table->decimal('rating', 5, 2)->nullable();
            $table->decimal('kd_ratio', 5, 2)->nullable();
            $table->decimal('impact_rating', 5, 2)->nullable();
            $table->decimal('adr', 6, 2)->nullable();
            $table->decimal('kast', 6, 2)->nullable();
            $table->decimal('kpr', 5, 2)->nullable();
            $table->decimal('dpr', 5, 2)->nullable();
            $table->decimal('headshot_percentage', 6, 2)->nullable();

            $table->unsignedInteger('maps_played')->nullable();
            $table->unsignedInteger('rounds_played')->nullable();

            $table->json('source_payload')->nullable();

            $table->timestamps();

            $table->index(['player_id', 'team_id', 'event_id', 'scope']);
            $table->index(['event_id', 'event_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_stat_snapshots');
    }
};

