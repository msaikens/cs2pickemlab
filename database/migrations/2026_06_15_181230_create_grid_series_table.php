<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_series', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_stage_id')->nullable()->constrained('event_stages')->nullOnDelete();

            $table->string('grid_series_id')->unique();
            $table->string('grid_tournament_id')->nullable();
            $table->string('grid_title_id')->nullable();

            $table->string('status')->default('discovered');
            // discovered, files_ready, downloaded, imported, failed

            $table->string('team_one_name')->nullable();
            $table->string('team_two_name')->nullable();
            $table->timestamp('starts_at')->nullable();

            $table->string('events_file_path')->nullable();
            $table->string('end_state_file_path')->nullable();

            $table->json('source_payload')->nullable();

            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();

            $table->index(['event_id', 'event_stage_id']);
            $table->index('grid_tournament_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_series');
    }
};