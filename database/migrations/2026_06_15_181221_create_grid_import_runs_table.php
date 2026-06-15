<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_import_runs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_stage_id')->nullable()->constrained('event_stages')->nullOnDelete();

            $table->string('action'); 
            // search_tournaments, discover_series, download_series_files,
            // import_files, import_stats, generate_predictions

            $table->string('status')->default('queued');
            // queued, running, completed, failed

            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->longText('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['action', 'status']);
            $table->index(['event_id', 'event_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_import_runs');
    }
};