<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnDelete();

            $table->foreignId('predicted_winner_team_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->unsignedTinyInteger('confidence_score')->default(50);
            // 0-100

            $table->string('upset_risk')->default('medium');
            // low, medium, high

            $table->string('best_pickem_use')->nullable();
            // safe_3_0, risky_3_0, safe_advancement, risky_advancement, avoid, upset_watch

            $table->string('status')->default('draft');
            // draft, published, archived

            $table->boolean('is_premium')->default(false);

            $table->string('headline')->nullable();
            $table->text('summary')->nullable();
            $table->longText('reasoning')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['is_premium', 'status']);
            $table->index('confidence_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
