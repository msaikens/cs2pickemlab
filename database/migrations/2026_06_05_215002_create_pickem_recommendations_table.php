<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickem_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('event_stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('slot_type');
            // safe_3_0, risky_3_0, safe_advancement, risky_advancement,
            // likely_0_3, upset_watch, avoid

            $table->string('risk_level')->default('medium');
            // low, medium, high

            $table->unsignedTinyInteger('confidence_score')->default(50);
            // 0-100

            $table->string('status')->default('draft');
            // draft, published, archived

            $table->boolean('is_premium')->default(false);

            $table->integer('sort_order')->default(0);

            $table->string('headline')->nullable();
            $table->text('summary')->nullable();
            $table->longText('reasoning')->nullable();

            $table->timestamps();

            $table->index(['event_id', 'event_stage_id']);
            $table->index(['slot_type', 'status']);
            $table->index(['is_premium', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickem_recommendations');
    }
};
