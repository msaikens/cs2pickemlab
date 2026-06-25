<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_appeals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('moderation_incident_id')
                ->constrained('moderation_incidents')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 64)->default('pending');

            $table->text('message');

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['moderation_incident_id', 'status']);
            $table->index('reviewed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_appeals');
    }
};